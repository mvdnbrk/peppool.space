import './bootstrap';

const REST_BASE = 'https://peppool.space/api';
const POLL_INTERVAL = 30000; // 30 seconds for polling
const CHECK_INTERVAL = 1000; // 1 second for checking element presence

function formatNumber(num) {
  return num.toLocaleString('en-US');
}

function updateBlockHeight(height) {
  const element = document.getElementById('current-block-height');
  if (element) {
    element.innerText = formatNumber(height);
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

let pollIntervalId = null;
let isVisible = document.visibilityState === 'visible';

function managePolling() {
  if (isVisible && document.getElementById('current-block-height')) {
    if (!pollIntervalId) {
      pollBlockHeight();
      pollIntervalId = setInterval(pollBlockHeight, POLL_INTERVAL);
    }
  } else {
    if (pollIntervalId) {
      clearInterval(pollIntervalId);
      pollIntervalId = null;
    }
  }
}

document.addEventListener('visibilitychange', () => {
  isVisible = document.visibilityState === 'visible';
  managePolling();
});

function startPollingWhenReady() {
  const checkElement = setInterval(() => {
    if (document.getElementById('current-block-height')) {
      clearInterval(checkElement);
      managePolling();
    }
  }, CHECK_INTERVAL);
}

document.addEventListener('DOMContentLoaded', startPollingWhenReady);
