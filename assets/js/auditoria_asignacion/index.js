document.addEventListener("DOMContentLoaded", () => {
    // Basic search functionality for the rendered table
    const searchInput = document.createElement("input");
    searchInput.type = "text";
    searchInput.placeholder = "Buscar en auditoría...";
    searchInput.className = "search-input mb-4";
    
    // Create an action bar
    const actionBar = document.createElement("div");
    actionBar.className = "action-bar";
    const flexContainer = document.createElement("div");
    flexContainer.className = "flex gap-4 items-center flex-1";
    const searchContainer = document.createElement("div");
    searchContainer.className = "search-container flex-1";
    
    searchContainer.appendChild(searchInput);
    flexContainer.appendChild(searchContainer);
    actionBar.appendChild(flexContainer);
    
    const tableContainer = document.querySelector(".table-container");
    tableContainer.parentNode.insertBefore(actionBar, tableContainer);

    searchInput.addEventListener("keyup", function() {
        let value = this.value.toLowerCase();
        let rows = document.querySelectorAll("#auditoriaTable tbody tr");
        
        rows.forEach(row => {
            // Ignore empty state row
            if(row.cells.length === 1) return;
            
            let text = row.textContent.toLowerCase();
            row.style.display = text.indexOf(value) > -1 ? "" : "none";
        });
    });
});
