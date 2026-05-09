// UI helpers
function toastCopy(text){
  navigator.clipboard.writeText(text).then(()=>{
    alert('Copied to clipboard!');
  });
}

function fireConfetti(){
  try{
    confetti({ particleCount: 200, spread: 70, origin: { y: 0.6 } });
  }catch(e){}
}

async function exportPDF(){
  const { jsPDF } = window.jspdf;
  const doc = new jsPDF();
  const title = document.querySelector('#settlementTitle')?.innerText || 'Expense Settlement Results';
  doc.text(title, 10, 10);
  const items = Array.from(document.querySelectorAll('#resultList li')).map(li => li.innerText);
  let y=20;
  items.forEach(line=>{ doc.text(line, 10, y); y+=10; });
  doc.save('settlements.pdf');
}

function shareWhatsApp(){
  const results = Array.from(document.querySelectorAll('#resultList li')).map(li=>li.innerText).join('\n');
  const text = '💰 Expense Split Results:\n' + results;
  window.open(`https://wa.me/?text=${encodeURIComponent(text)}`, '_blank');
}

// AJAX helper
async function postJSON(url, data){
  const res = await fetch(url, {
    method:'POST',
    headers:{ 'Content-Type':'application/json' },
    body: JSON.stringify(data)
  });
  return await res.json();
}
