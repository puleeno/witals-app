import { render } from 'solid-js/web';
import { createSignal, onMount, For } from 'solid-js';
import Dashboard from './Dashboard.jsx';

// Mount the SPA into the shell rendered by DashboardController (#admin-app).
// If the mount point is absent (e.g. cached page), do nothing.
const root = document.getElementById('admin-app');
if (root) {
  render(() => <Dashboard />, root);
}

export { Dashboard };