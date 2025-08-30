// Expose polling utilities and an Alpine component for mempool txids + count.
// This file intentionally does NOT initialize Alpine. It is imported from app.js.

const REST_BASE = import.meta.env.VITE_APP_URL ? `${import.meta.env.VITE_APP_URL}/api` : '/api';

// Intervals
const POLL_HEIGHT_INTERVAL = 30000; // 30s
const POLL_PRICE_INTERVAL = 300000; // 5m
const CHECK_INTERVAL = 1000; // 1s

function formatNumber(num) { return num.toLocaleString('en-US'); }

function updatePepecoinPrice(price) {
  const el = document.getElementById('pepecoin-price');
  if (el) el.innerText = `$${parseFloat(price).toFixed(8)}`;
}

function updateBlockHeight(height) {
  const el = document.getElementById('current-block-height');
  if (el) el.innerText = formatNumber(height);
}

async function pollBlockHeight() {
  try {
    const res = await fetch(`${REST_BASE}/blocks/tip/height`);
    if (!res.ok) throw new Error('Network response was not ok');
    const heightText = await res.text();
    const height = parseInt(heightText, 10);
    if (!isNaN(height)) updateBlockHeight(height);
  } catch (e) {
    console.error('Error polling block height:', e);
  }
}

async function pollPepecoinPrice() {
  try {
    const res = await fetch(`${REST_BASE}/prices`);
    if (!res.ok) throw new Error('Network response was not ok');
    const data = await res.json();
    updatePepecoinPrice(data.USD);
  } catch (e) {
    console.error('Error fetching Pepecoin price:', e);
  }
}

let pollHeightIntervalId = null;
let pollPriceIntervalId = null;
let isVisible = document.visibilityState === 'visible';

function managePolling() {
  if (!isVisible) {
    if (pollHeightIntervalId) { clearInterval(pollHeightIntervalId); pollHeightIntervalId = null; }
    if (pollPriceIntervalId) { clearInterval(pollPriceIntervalId); pollPriceIntervalId = null; }
    return;
  }
  pollBlockHeight();
  pollHeightIntervalId = setInterval(pollBlockHeight, POLL_HEIGHT_INTERVAL);

  pollPepecoinPrice();
  pollPriceIntervalId = setInterval(pollPepecoinPrice, POLL_PRICE_INTERVAL);
}

document.addEventListener('visibilitychange', () => {
  isVisible = document.visibilityState === 'visible';
  managePolling();
});

function startPollingWhenReady() {
  const checkElement = setInterval(() => {
    if (
      document.getElementById('current-block-height') ||
      document.getElementById('mempool-count') ||
      document.getElementById('pepecoin-price')
    ) {
      clearInterval(checkElement);
      managePolling();
    }
  }, CHECK_INTERVAL);
}

document.addEventListener('DOMContentLoaded', startPollingWhenReady);

window.mempoolWidget = ({ apiUrl, intervalMs = 10000, initialTxids = [] } = {}) => ({
  txids: initialTxids,
  // Track when each txid was first seen to render newest-first regardless of API order
  firstSeen: {},
  _timer: null,
  _inFlight: false,
  _controller: null,

  init() {
    // Initialize firstSeen timestamps for initial txids so their relative order is preserved
    const now = Date.now();
    for (let i = 0; i < this.txids.length; i++) {
      const id = this.txids[i];
      if (!this.firstSeen[id]) this.firstSeen[id] = now - (this.txids.length - i);
    }
    // Ensure sorted newest-first by firstSeen
    this.txids.sort((a, b) => (this.firstSeen[b] || 0) - (this.firstSeen[a] || 0));
    this.updateCount();
    this.fetchTxids();
    if (!this._timer) {
      this._timer = setInterval(() => this.fetchTxids(), intervalMs);
    }

    // Pause/resume polling on tab visibility changes
    document.addEventListener('visibilitychange', () => {
      const visible = document.visibilityState === 'visible';
      if (!visible && this._timer) {
        clearInterval(this._timer);
        this._timer = null;
      } else if (visible && !this._timer) {
        this._timer = setInterval(() => this.fetchTxids(), intervalMs);
      }
    });
  },

  updateCount() {
    const el = document.getElementById('mempool-count');
    if (el) el.innerText = (this.txids.length || 0).toLocaleString('en-US');
  },

  async fetchTxids() {
    if (this._inFlight) return;
    this._inFlight = true;
    if (this._controller) {
      try { this._controller.abort(); } catch (_) {}
    }
    this._controller = new AbortController();
    const { signal } = this._controller;
    try {
      const url = apiUrl ?? `${REST_BASE}/mempool/txids`;
      const res = await fetch(url, { signal });
      if (!res.ok) return;
      const data = await res.json();
      if (!Array.isArray(data)) return;

      // Merge strategy: detect new txids (API order not guaranteed)
      const existing = new Set(this.txids);
      const newOnes = data.filter((txid) => !existing.has(txid));

      if (newOnes.length) {
        // Record firstSeen for new txids
        const now = Date.now();
        for (let i = 0; i < newOnes.length; i++) {
          const id = newOnes[i];
          if (!this.firstSeen[id]) this.firstSeen[id] = now - (newOnes.length - i);
        }

        // Merge new ones with existing, dedupe
        const merged = [...newOnes, ...this.txids];
        const seen = new Set();
        this.txids = merged.filter((t) => (seen.has(t) ? false : (seen.add(t), true)));

        // Sort by firstSeen desc so newest arrivals display first
        this.txids.sort((a, b) => (this.firstSeen[b] || 0) - (this.firstSeen[a] || 0));

        // Optional cap to avoid unbounded growth
        const MAX_ITEMS = 200;
        if (this.txids.length > MAX_ITEMS) this.txids.length = MAX_ITEMS;
        this.updateCount();
      }
    } catch (err) {
      // ignore abort errors
      if (err?.name !== 'AbortError') {
        // swallow other errors
      }
    } finally {
      this._inFlight = false;
      this._controller = null;
    }
  }
});
