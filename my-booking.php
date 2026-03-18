<?php
require_once 'php/config.php';
$page_title = 'Track My Booking | CSNExplore';
$current_page = 'my-booking.php';
require 'header.php';
?>
<main class="min-h-screen bg-slate-50 py-12">
  <div class="max-w-xl mx-auto px-4">
    <div class="text-center mb-8">
      <span class="material-symbols-outlined text-5xl text-primary mb-3 block">confirmation_number</span>
      <h1 class="text-2xl font-serif font-black text-slate-900 mb-2">Track Your Booking</h1>
      <p class="text-slate-500 text-sm">Enter your phone number to find your bookings</p>
    </div>

    <div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
      <div class="flex gap-3">
        <input id="track-phone" type="tel" placeholder="+91 XXXXX XXXXX"
               class="flex-1 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30"/>
        <button onclick="trackBooking()" class="bg-primary text-white font-bold px-6 py-3 rounded-xl text-sm hover:bg-orange-600 transition-all">
          Search
        </button>
      </div>
      <div id="track-error" class="hidden mt-3 text-red-600 text-sm font-semibold"></div>
    </div>

    <div id="results" class="space-y-4 hidden">
      <h2 class="font-bold text-slate-700 text-sm uppercase tracking-wider">Your Bookings</h2>
      <div id="results-list"></div>
    </div>

    <div id="no-results" class="hidden text-center py-10 text-slate-400">
      <span class="material-symbols-outlined text-4xl mb-2 block">search_off</span>
      <p class="font-semibold">No bookings found for this number</p>
      <p class="text-xs mt-1">Make sure you enter the same number used during booking</p>
    </div>
  </div>
</main>

<script>
async function trackBooking() {
  var phone = document.getElementById('track-phone').value.trim();
  var errEl = document.getElementById('track-error');
  errEl.classList.add('hidden');
  if (!phone) { errEl.textContent = 'Please enter your phone number'; errEl.classList.remove('hidden'); return; }

  try {
    var res = await fetch('php/api/track_booking.php?phone=' + encodeURIComponent(phone));
    var data = await res.json();
    if (!res.ok) { errEl.textContent = data.error || 'Something went wrong'; errEl.classList.remove('hidden'); return; }

    var list = document.getElementById('results-list');
    if (!data.length) {
      document.getElementById('results').classList.add('hidden');
      document.getElementById('no-results').classList.remove('hidden');
      return;
    }
    document.getElementById('no-results').classList.add('hidden');
    document.getElementById('results').classList.remove('hidden');

    list.innerHTML = data.map(function(b) {
      var sc = b.status === 'pending'
        ? 'bg-orange-100 text-orange-700'
        : b.status === 'completed'
        ? 'bg-green-100 text-green-700'
        : 'bg-red-100 text-red-700';
      var icon = b.status === 'pending' ? 'schedule' : b.status === 'completed' ? 'check_circle' : 'cancel';
      return '<div class="bg-white rounded-2xl shadow-sm p-5 border border-slate-100">'
        + '<div class="flex items-start justify-between mb-3">'
        + '<div>'
        + '<p class="font-bold text-slate-900">' + escHtml(b.listing_name || b.service_type) + '</p>'
        + '<p class="text-xs text-slate-400 mt-0.5">Booking #' + b.id + ' · ' + escHtml(b.created_at.split(' ')[0]) + '</p>'
        + '</div>'
        + '<span class="flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold ' + sc + '">'
        + '<span class="material-symbols-outlined text-sm">' + icon + '</span>' + b.status.charAt(0).toUpperCase() + b.status.slice(1)
        + '</span>'
        + '</div>'
        + '<div class="grid grid-cols-2 gap-3 text-sm">'
        + '<div><p class="text-xs text-slate-400">Name</p><p class="font-semibold">' + escHtml(b.full_name) + '</p></div>'
        + '<div><p class="text-xs text-slate-400">Phone</p><p class="font-semibold">' + escHtml(b.phone) + '</p></div>'
        + (b.booking_date ? '<div><p class="text-xs text-slate-400">Date</p><p class="font-semibold">' + escHtml(b.booking_date) + '</p></div>' : '')
        + '<div><p class="text-xs text-slate-400">People</p><p class="font-semibold">' + b.number_of_people + '</p></div>'
        + '<div><p class="text-xs text-slate-400">Service</p><p class="font-semibold capitalize">' + escHtml(b.service_type) + '</p></div>'
        + '</div>'
        + '</div>';
    }).join('');
  } catch(e) {
    errEl.textContent = 'Network error. Please try again.';
    errEl.classList.remove('hidden');
  }
}

function escHtml(s) {
  return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

document.getElementById('track-phone').addEventListener('keydown', function(e) {
  if (e.key === 'Enter') trackBooking();
});
</script>

<?php require 'footer.php'; ?>
