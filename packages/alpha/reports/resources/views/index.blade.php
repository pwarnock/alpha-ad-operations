<!DOCTYPE html>
<html>
<head>
    <title>Reports</title>
</head>
<body>
    <h1>Custom Reports</h1>
    <form action="{{ route('reports.generate') }}" method="GET">
        <label for="rep">Rep ID:</label>
        <input type="text" name="rep" id="rep" placeholder="Enter rep ID"><br>

        <label for="advertiser">Advertiser ID:</label>
        <input type="text" name="advertiser" id="advertiser" placeholder="Enter advertiser ID"><br>

        <label for="product">Product:</label>
        <input type="text" name="product" id="product" placeholder="Enter product name"><br>

        <label for="start_date">Start Date:</label>
        <input type="date" name="start_date" id="start_date"><br>

        <label for="end_date">End Date:</label>
        <input type="date" name="end_date" id="end_date"><br>

        <button type="submit">Generate Report</button>
    </form>
</body>
</html>
