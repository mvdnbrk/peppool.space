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
  if (!el) return;
  el.innerText = `$${price}`;
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

// Legacy mempool widget removed - now handled by Vue.js component
