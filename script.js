document.getElementById('updateForm').addEventListener('submit', function(event) {
    event.preventDefault();
    
    const tableName = document.getElementById('tableName').value;
    const columnName = document.getElementById('columnName').value;
    const newValue = document.getElementById('newValue').value;
    const rowId = document.getElementById('rowId').value;

    const formData = new FormData();
    formData.append('tableName', tableName);
    formData.append('columnName', columnName);
    formData.append('newValue', newValue);
    formData.append('rowId', rowId);

    fetch('update.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        alert(data);
    });
});

document.getElementById('combineForm').addEventListener('submit', function(event) {
    event.preventDefault();

    const table1 = document.getElementById('table1').value;
    const table2 = document.getElementById('table2').value;

    const formData = new FormData();
    formData.append('table1', table1);
    formData.append('table2', table2);

    fetch('combine.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        const resultDiv = document.getElementById('combineResult');
        resultDiv.innerHTML = '<pre>' + JSON.stringify(data, null, 2) + '</pre>';
    });
});
