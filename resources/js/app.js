import './bootstrap';
import './timestamps';

const REST_BASE = import.meta.env.VITE_APP_URL ? `${import.meta.env.VITE_APP_URL}/api` : '/api';
const POLL_HEIGHT_INTERVAL = 30000; // 30 seconds for polling
const POLL_MEMPOOL_INTERVAL = 10000; // 10 seconds for polling
const CHECK_INTERVAL = 1000; // 1 second for checking element presence

let pollHeightIntervalId = null;
let pollMempoolIntervalId = null;
let isVisible = document.visibilityState === 'visible';

function formatNumber(num) {
  return num.toLocaleString('en-US');
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

    return;
  }

  pollBlockHeight();
  pollHeightIntervalId = setInterval(pollBlockHeight, POLL_HEIGHT_INTERVAL);

  pollMempoolCount();
  pollMempoolIntervalId = setInterval(pollMempoolCount, POLL_MEMPOOL_INTERVAL);
}

document.addEventListener('visibilitychange', () => {
  isVisible = document.visibilityState === 'visible';
  managePolling();
});

function startPollingWhenReady() {
  const checkElement = setInterval(() => {
    if (
        document.getElementById('current-block-height') ||
        document.getElementById('mempool-count')
    ) {
      clearInterval(checkElement);
      managePolling();
    }
  }, CHECK_INTERVAL);
}

document.addEventListener('DOMContentLoaded', startPollingWhenReady);
