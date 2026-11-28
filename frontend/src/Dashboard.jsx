import { createSignal, onMount, For, Show } from 'solid-js';

/**
 * Headless dashboard: fetches stats from /api/dashboard/stats and renders them.
 * The server-rendered cards remain as a no-JS fallback; this replaces the
 * mount node content once data arrives.
 */
export default function Dashboard() {
  const [stats, setStats] = createSignal([]);
  const [loading, setLoading] = createSignal(true);
  const [error, setError] = createSignal('');

  onMount(async () => {
    try {
      const res = await fetch('/api/dashboard/stats', {
        headers: { Accept: 'application/json' },
      });
      if (!res.ok) throw new Error(`HTTP ${res.status}`);
      const data = await res.json();
      setStats(data.stats ?? []);
    } catch (e) {
      setError(String(e));
    } finally {
      setLoading(false);
    }
  });

  return (
    <div class="admin-spa" data-spa="dashboard">
      <Show when={loading()} fallback={
        <Show when={!error()} fallback={<p class="spa-error">Không tải được dữ liệu: {error()}</p>}>
          <div class="admin-dashboard-grid">
            <For each={stats()}>
              {(s) => (
                <div class="admin-stat-card">
                  <div class="stat-icon">{s.icon}</div>
                  <div class="stat-value">{s.value}</div>
                  <div class="stat-label">{s.label}</div>
                </div>
              )}
            </For>
          </div>
        </Show>
      }>
        <div class="admin-dashboard-grid"><div class="admin-stat-card">Đang tải…</div></div>
      </Show>
    </div>
  );
}