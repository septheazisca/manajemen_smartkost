<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  /* Tanggal */
  const d = new Date();
  document.getElementById('dateToday').textContent =
    d.toLocaleDateString('id-ID',{weekday:'long',year:'numeric',month:'long',day:'numeric'});

  /* Bar chart */
  const data = [65, 72, 58, 80, 75, 92];
  const max  = Math.max(...data);
  const chart = document.getElementById('barChart');
  data.forEach((v, i) => {
    const bar = document.createElement('div');
    bar.className = 'mini-bar' + (i === data.length - 1 ? ' active' : '');
    bar.style.height = (v / max * 100) + '%';
    bar.title = `Rp ${v * 200}rb`;
    chart.appendChild(bar);
  });

  /* Sidebar toggle */
  function toggleSidebar() {
    document.getElementById('sidebar').classList.toggle('open');
    document.getElementById('sidebarOverlay').classList.toggle('show');
  }
  function closeSidebar() {
    document.getElementById('sidebar').classList.remove('open');
    document.getElementById('sidebarOverlay').classList.remove('show');
  }
