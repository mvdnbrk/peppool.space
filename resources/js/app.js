import './bootstrap';
import './timestamps';
import './development-notice';

const REST_BASE = import.meta.env.VITE_APP_URL ? `${import.meta.env.VITE_APP_URL}/api` : '/api';
const POLL_HEIGHT_INTERVAL = 30000; // 30 seconds for polling
const POLL_MEMPOOL_INTERVAL = 10000; // 10 seconds for polling
const POLL_PRICE_INTERVAL = 300000; // 5 minutes for price polling
const CHECK_INTERVAL = 1000; // 1 second for checking element presence

let pollHeightIntervalId = null;
let pollMempoolIntervalId = null;
let pollPriceIntervalId = null;
let isVisible = document.visibilityState === 'visible';

function formatNumber(num) {
  return num.toLocaleString('en-US');
}

function updatePepecoinPrice(price) {
    const element = document.getElementById('pepecoin-price');
    if (element) {
        element.innerText = `$${parseFloat(price).toFixed(8)}`;
    }
}

function updateBlockHeight(height) {
  const element = document.getElementById('current-block-height');
  if (element) {
    element.innerText = formatNumber(height);
  }
}

function updateMempoolCount(count) {
  const element = document.getElementById('mempool-count');
  if (element) {
    element.innerText = formatNumber(count);
  }
}

async function pollBlockHeight() {
  try {
    const response = await fetch(`${REST_BASE}/blocks/tip/height`);
    if (!response.ok) {
      throw new Error('Network response was not ok');
    }
    const heightText = await response.text();
    const height = parseInt(heightText, 10);
    if (!isNaN(height)) {
      updateBlockHeight(height);
    }
  } catch (error) {
    console.error('Error polling block height:', error);
  }
}

async function pollMempoolCount() {
  try {
    const response = await fetch(`${REST_BASE}/mempool`);
    if (!response.ok) {
      throw new Error('Network response was not ok');
    }
    const data = await response.json();
    const count = data.count;
    if (!isNaN(count)) {
      updateMempoolCount(count);
    }
  } catch (error) {
    console.error('Error polling mempool count:', error);
  }
}

async function pollPepecoinPrice() {
    try {
        const response = await fetch(`${REST_BASE}/prices`);
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        const data = await response.json();
        updatePepecoinPrice(data.USD);
    } catch (error) {
        console.error('Error fetching Pepecoin price:', error);
    }
}

function managePolling() {
  if (!isVisible) {
    if (pollHeightIntervalId) {
      clearInterval(pollHeightIntervalId);
      pollHeightIntervalId = null;
    }
    if (pollMempoolIntervalId) {
      clearInterval(pollMempoolIntervalId);
      pollMempoolIntervalId = null;
    }
    if (pollPriceIntervalId) {
      clearInterval(pollPriceIntervalId);
      pollPriceIntervalId = null;
    }
    return;
  }

  pollBlockHeight();
  pollHeightIntervalId = setInterval(pollBlockHeight, POLL_HEIGHT_INTERVAL);

  pollMempoolCount();
  pollMempoolIntervalId = setInterval(pollMempoolCount, POLL_MEMPOOL_INTERVAL);

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
