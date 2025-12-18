<style>
  /* ============================================
     1. SCROLL WRAPPER (CONTAINER UTAMA)
     ============================================ */
  .table-scroll-wrapper {
    width: 100%;
    /* Gunakan max-height. 
       Artinya: Jika data cuma 2 baris, tinggi wrapper ikut memendek.
       Jika data 100 baris, tinggi wrapper mentok di 70vh lalu scroll muncul. */
    height: auto;
    max-height: 70vh;

    overflow: auto;
    position: relative;
    border: 1px solid #dee2e6;
    background: white;
    isolation: isolate;
  }

  /* Custom Scrollbar */
  .table-scroll-wrapper::-webkit-scrollbar {
    width: 12px;
    height: 12px;
  }

  .table-scroll-wrapper::-webkit-scrollbar-track {
    background: #f1f1f1;
  }

  .table-scroll-wrapper::-webkit-scrollbar-thumb {
    background-color: #003366;
    border-radius: 6px;
    border: 3px solid #f1f1f1;
  }

  /* ============================================
     2. TABLE BASE
     ============================================ */
  .attendance-matrix-table {
    width: auto;
    min-width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    margin: 0;
    font-size: 11px;
  }

  .attendance-matrix-table th,
  .attendance-matrix-table td {
    border-right: 1px solid #ccc;
    border-bottom: 1px solid #ccc;
    padding: 8px 6px;
    text-align: center;
    vertical-align: middle;
    white-space: nowrap;
  }

  .attendance-matrix-table thead tr:first-child th {
    border-top: 1px solid #ccc;
  }

  .attendance-matrix-table tbody tr td:first-child {
    border-left: 1px solid #ccc;
  }

  /* ============================================
     3. STICKY HEADERS & COLUMNS
     ============================================ */
  .attendance-matrix-table thead {
    position: sticky;
    top: 0;
    z-index: 20;
  }

  .header-main th {
    background: #003366;
    color: white;
    padding: 10px 6px;
    font-weight: bold;
  }

  .header-date th {
    background: #004080;
    color: white;
    padding: 8px 6px;
    font-weight: bold;
  }

  /* Sticky Column (Kiri) */
  .col-sticky {
    position: sticky;
    z-index: 10;
    background: white;
    outline: 1px solid #ccc;
    outline-offset: -1px;
  }

  .col-no {
    left: 0;
    width: 40px;
    min-width: 40px;
    max-width: 40px;
  }

  .col-nama {
    left: 40px;
    width: 220px;
    min-width: 220px;
    max-width: 220px;
    text-align: left !important;
    box-shadow: 4px 0 5px -2px rgba(0, 0, 0, 0.2);
    clip-path: inset(0px -10px 0px 0px);
  }

  thead .col-sticky {
    z-index: 30 !important;
    background: #003366 !important;
    color: white;
    outline: 1px solid #004080;
  }

  /* ============================================
     4. DATA CELLS
     ============================================ */
  .col-divisi {
    width: 120px;
    min-width: 120px;
  }

  .col-hari,
  .col-tanggal {
    width: 50px;
    min-width: 50px;
  }

  /* WAKTU CONTAINER */
  .waktu-container {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    gap: 0;
    font-size: 10px;
    line-height: 1.1;
  }

  .waktu-normal {
    color: #0066cc;
    font-weight: 600;
  }

  .waktu-telat {
    color: #dc3545;
    font-weight: bold;
  }

  .label-telat {
    font-size: 8px;
    color: #dc3545;
  }

  .badge-cell {
    display: inline-block;
    font-weight: bold;
    padding: 2px 6px;
    border-radius: 3px;
    background: rgba(0, 0, 0, 0.1);
  }

  /* ============================================
     5. COLORS (WARNA TOTAL JADI HITAM)
     ============================================ */
  /* Kolom Total Header */
  .col-total-group {
    background: #1a1a1a !important;
    color: white;
  }

  /* Kolom Total Body (Angka) - Tulisan HITAM */
  .total-h {
    background: #d4edda !important;
    color: black !important;
    font-weight: bold;
  }

  .total-si {
    background: #fff3cd !important;
    color: black !important;
    font-weight: bold;
  }

  .total-a {
    background: #f8d7da !important;
    color: black !important;
    font-weight: bold;
  }

  .total-t {
    background: #d1ecf1 !important;
    color: black !important;
    font-weight: bold;
  }

  /* Status Colors */
  .status-alpha {
    background: #ff4444 !important;
    color: white;
  }

  .status-libur {
    background: #ff9900 !important;
    color: white;
  }

  .status-sakit {
    background: #ffeb3b !important;
    color: #333;
  }

  .status-izin {
    background: #ffeb3b !important;
    color: #333;
  }

  .status-belum {
    background: #e0e0e0 !important;
    color: #666;
  }

  /* LEGEND */
  .legend-items {
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
    margin-top: 5px;
  }

  .legend-item {
    display: flex;
    align-items: center;
    gap: 5px;
    font-size: 12px;
  }

  .legend-color {
    width: 15px;
    height: 15px;
    border: 1px solid #999;
  }

  .bg-alpha {
    background: #ff4444;
  }

  .bg-libur {
    background: #ff9900;
  }

  .bg-sakit {
    background: #ffeb3b;
  }

  .bg-hadir {
    background: white;
  }

  .bg-belum {
    background: #e0e0e0;
  }

  /* Warna Abu-abu */

  /* Hover */
  .attendance-matrix-table tbody tr:hover td {
    background-color: #eef2f7;
  }

  .attendance-matrix-table tbody tr:hover td.status-alpha {
    background-color: #ff5555 !important;
  }

  /* ============================================
     6. RESPONSIVE MOBILE
     ============================================ */
  @media (max-width: 768px) {
    .attendance-matrix-table {
      font-size: 10px;
    }

    .attendance-matrix-table th,
    .attendance-matrix-table td {
      padding: 4px;
    }

    .col-no {
      width: 30px;
      min-width: 30px;
      max-width: 30px;
    }

    .col-nama {
      left: 30px;
      width: 130px;
      min-width: 130px;
      max-width: 130px;
    }

    .col-hari,
    .col-tanggal {
      width: 45px;
      min-width: 45px;
    }
  }
</style>