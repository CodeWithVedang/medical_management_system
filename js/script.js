// Reset form fields (for Medicine Management)
function resetForm() {
    document.getElementById('medicineForm').reset();
    document.getElementById('medicineId').value = '';
    document.getElementById('submitBtn').textContent = 'Add Medicine';
}

// Populate form for editing (for Medicine Management)
function editMedicine(id, name, type, expiry_date, price, stock) {
    document.getElementById('medicineId').value = id;
    document.getElementById('name').value = name;
    document.getElementById('type').value = type;
    document.getElementById('expiry_date').value = expiry_date;
    document.getElementById('price').value = price;
    document.getElementById('stock').value = stock;
    document.getElementById('submitBtn').textContent = 'Update Medicine';
    window.scrollTo(0, 0);
}

// Confirm deletion (for Medicine Management)
function deleteMedicine(id) {
    if (confirm('Are you sure you want to delete this medicine?')) {
        window.location.href = `php/delete_medicine.php?id=${id}`;
    }
}

// Toggle navbar for mobile
document.addEventListener('DOMContentLoaded', function() {
    const navLinks = document.querySelector('.nav-links');
    const toggleButton = document.createElement('button');
    toggleButton.textContent = 'Menu';
    toggleButton.className = 'menu-toggle';
    toggleButton.style.display = 'none';
    document.querySelector('.navbar').prepend(toggleButton);

    toggleButton.addEventListener('click', () => {
        navLinks.style.display = navLinks.style.display === 'none' ? 'flex' : 'none';
    });

    function adjustNavbar() {
        if (window.innerWidth <= 768) {
            toggleButton.style.display = 'block';
            navLinks.style.display = 'none';
        } else {
            toggleButton.style.display = 'none';
            navLinks.style.display = 'flex';
        }
    }

    adjustNavbar();
    window.addEventListener('resize', adjustNavbar);
});


// Hamburger menu toggle for mobile
document.addEventListener('DOMContentLoaded', function () {
    const menuToggle = document.querySelector('.menu-toggle');
    const navLinks = document.querySelector('.nav-links');

    if (menuToggle && navLinks) {
        menuToggle.addEventListener('click', function () {
            navLinks.classList.toggle('active');
        });
    }
});