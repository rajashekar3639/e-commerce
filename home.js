document.addEventListener('DOMContentLoaded', function() {
    var searchBtn = document.getElementById('search-btn');
    var searchForm = document.getElementById('search-form');
    var profileBtn = document.getElementById('profile-btn');
    var profileDropdown = document.getElementById('profile-dropdown');

    searchBtn.onclick = function() {
        if (searchForm.style.display === "flex") {
            searchForm.style.display = "none";
        } else {
            searchForm.style.display = "flex";
        }
    }

    profileBtn.onclick = function() {
        if (profileDropdown.style.display === "block") {
            profileDropdown.style.display = "none";
        } else {
            profileDropdown.style.display = "block";
        }
    }

    window.onclick = function(event) {
        if (event.target !== searchBtn && event.target !== searchForm && !searchForm.contains(event.target)) {
            searchForm.style.display = "none";
        }
        if (event.target !== profileBtn && event.target !== profileDropdown && !profileDropdown.contains(event.target)) {
            profileDropdown.style.display = "none";
        }
    }

});