export function reinitializeDataTable(tableSelector = '#dataTable') {
    const $ = window.$;
    const $table = $(tableSelector);

    if ($table.length && $.fn.DataTable.isDataTable($table[0])) {
        $table.DataTable().destroy();
    }

    $table.DataTable({
        responsive: true,
        lengthChange: false,
        info: false,
    });
}

export const dataTableActionsClickHandler = (editFn, deleteFn) => (event) => {
    const editBtn = event.target.closest('.edit-btn');
    const deleteBtn = event.target.closest('.delete-btn');

    if (editBtn) {
        const id = parseInt(editBtn.dataset.id);
        if (!isNaN(id)) editFn(id);
    } else if (deleteBtn) {
        const id = parseInt(deleteBtn.dataset.id);
        if (!isNaN(id)) deleteFn(id);
    }
};

/**
 * Converts Array of objects to 2D Array
 * used as an adapter between api responses and datatable
 * ignore id field, since id will be add nonetheless
 */
export function objectArrayTo2D(data, options = {}) {
    const { includeIndex = true, columns = [] } = options;
    if (!Array.isArray(data) || data.length === 0) return [];

    return data.map((item, index) => {
        const row = columns.map(k => item[k]);

        // Add the ID as first column
        row.unshift(item.id);

        if (includeIndex) {
            // If you want a row number, add it after the id (e.g., at index 1)
            row.splice(1, 0, index + 1); 
        }

        return row;
    }).map(row => {
        return [...row, null];
    });
}