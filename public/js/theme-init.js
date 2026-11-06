(() => {
  try {
    document.documentElement.dataset.theme = window.localStorage.getItem('metalcom-theme') === 'dark' ? 'dark' : 'light';
  } catch (_) {
    document.documentElement.dataset.theme = 'light';
  }
})();
