import './bootstrap';

import Alpine from 'alpinejs';
import Chart from 'chart.js/auto';

window.Alpine = Alpine;
window.Chart = Chart;

Alpine.start();

/* ── Count-up animation on scroll ── */
function initCountUp() {
  const counters = document.querySelectorAll('[data-countup]');
  if (!counters.length) return;
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (!entry.isIntersecting) return;
      const el = entry.target;
      const target = parseInt(el.dataset.countup, 10);
      const duration = 1500;
      const start = performance.now();
      function easeOut(t) { return 1 - Math.pow(1 - t, 3); }
      function step(now) {
        const elapsed = Math.min((now - start) / duration, 1);
        el.textContent = Math.round(easeOut(elapsed) * target).toLocaleString('id-ID');
        if (elapsed < 1) requestAnimationFrame(step);
      }
      requestAnimationFrame(step);
      observer.unobserve(el);
    });
  }, { threshold: 0.5 });
  counters.forEach(el => observer.observe(el));
}
document.addEventListener('DOMContentLoaded', initCountUp);

/* ── Chart.js highlighted-point plugin ── */
window.highlightPlugin = {
  id: 'highlightPoint',
  afterDraw(chart) {
    const activeIndex = chart.options.plugins.highlightPoint?.activeIndex;
    if (activeIndex == null) return;
    const meta = chart.getDatasetMeta(0);
    const point = meta.data[activeIndex];
    if (!point) return;
    const { x, y } = point.getProps(['x', 'y'], true);
    const ctx = chart.ctx;
    const bottom = chart.chartArea.bottom;
    // Vertical dashed line
    ctx.save();
    ctx.setLineDash([4, 4]);
    ctx.strokeStyle = 'rgba(22,163,74,.35)';
    ctx.lineWidth = 1.5;
    ctx.beginPath();
    ctx.moveTo(x, y);
    ctx.lineTo(x, bottom);
    ctx.stroke();
    ctx.restore();
    // Filled circle
    ctx.save();
    ctx.beginPath();
    ctx.arc(x, y, 8, 0, Math.PI * 2);
    ctx.fillStyle = '#16a34a';
    ctx.fill();
    ctx.strokeStyle = '#fff';
    ctx.lineWidth = 2;
    ctx.stroke();
    ctx.restore();
  }
};
