document.addEventListener('DOMContentLoaded', () => {
  const vendorTable = document.getElementById("vendor-table");
  const pendingCountElem = document.getElementById("pending-count");

  function loadVendors() {
    fetch('get_vendors.php')
      .then(res => res.json())
      .then(vendors => {
        vendorTable.innerHTML = '';
        const pendingVendors = vendors.filter(v => v.status.toLowerCase() === 'pending');
        pendingCountElem.innerText = pendingVendors.length;

        vendors.forEach(vendor => {
          const tr = document.createElement('tr');

          tr.innerHTML = `
            <td>${vendor.name}</td>
            <td>${vendor.email}</td>
            <td>${vendor.request_date}</td>
            <td data-label="Status" class="${vendor.status.toLowerCase()}">${vendor.status}</td>
            <td data-label="Action">
              ${vendor.status.toLowerCase() === 'pending' ?
                `<button data-id="${vendor.id}">Approve</button>` :
                `<button disabled>Approved</button>`}
            </td>
          `;

          vendorTable.appendChild(tr);
        });

        vendorTable.querySelectorAll('button').forEach(button => {
          if (!button.disabled) {
            button.addEventListener('click', () => approveVendor(button));
          }
        });
      })
      .catch(err => console.error('Error loading vendors:', err));
  }

  function approveVendor(button) {
    const vendorId = button.getAttribute('data-id');
    fetch('approve_vendor.php', {
      method: 'POST',
      headers: {'Content-Type': 'application/json'},
      body: JSON.stringify({id: vendorId})
    })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        const row = button.closest("tr");
        const statusCell = row.querySelector('td[data-label="Status"]');

        statusCell.innerText = "Approved";
        statusCell.classList.remove("pending");
        statusCell.classList.add("approved");

        button.disabled = true;
        button.innerText = "Approved";

        let count = parseInt(pendingCountElem.innerText);
        if (count > 0) {
          pendingCountElem.innerText = count - 1;
        }
      } else {
        alert('Failed to approve vendor: ' + (data.error || 'Unknown error'));
      }
    })
    .catch(err => {
      alert('Error approving vendor');
      console.error(err);
    });
  }

  loadVendors();
});
