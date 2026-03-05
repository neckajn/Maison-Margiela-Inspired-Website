* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

html, body {
    height: 100%;
    width: 100%;
    overflow-y: auto; 
    overflow-x: hidden;
}

.container {
    display: flex;
    width: 100%;
    min-height: 100%;
    gap: 20px; 
}


.sidebar {
    width: 200px;
    background-color: #1d1d1d;
    color: white;
    padding-top: 20px;
    position: fixed;
    top: 15px;
    left: 0;
    bottom: 20px;
    z-index: 10;
    justify-content: flex-start;

    border-top-right-radius: 20px;    
    border-bottom-right-radius: 20px; 

    padding-top: 50px;    
    padding-bottom: 50px;
}

.sidebar ul {
    list-style: none;
    padding: 5;

}

.sidebar ul li {
    padding: 11px;
    text-align: left;
    
}

.sidebar ul li a {
    color: white;
    font-family: 'Goudy', sans-serif;
    font-size: 18px;
    font-weight: bold;
    text-decoration: none;
    padding-left: 15px;

}

.main-content {
    margin-left: 200px;
    padding: 150px;
    margin-left: 6%;
    background-color: #ffffff;
    width: calc(100% - 200px);
    min-height: 100%;
}

.maisonmargiela-container {
    position: absolute;
    top: 4%;
    left: 57.9%;
    transform: translateX(-50%);
    font-size: 30px;
    color: rgb(9, 8, 8);
    font-weight: 550;
    text-align: center;
    z-index: 1;
    font-family: 'Goudy', serif;
}

.paris-container {
    position: absolute;
    top: 8.5%;
    left: 56%;
    font-size: 14px;
    font-family: 'Goudy', serif;
}

.add-product {
    background-color: #1d1d1d; 
    color: white; 
    padding: 10px 20px; 
    font-size: 16px; 
    border: none; 
    cursor: pointer; 
    border-radius: 5px; 
    transition: background-color 0.3s ease; 
    font-family: 'Goudy', sans-serif;
    font-weight: bold;
    margin-bottom: 30px;
    
    max-width: 500px;
    width: 16.8%;

}

.add-product:hover {
    background-color: #777777; 
}

a {
    text-decoration: none; 
}

.modal-content {
    background-color: #fff;
    padding: 20px 20px;
    width: 400px;
    margin-right: 3px;
    margin-left: 3px;
}

input[type="text"], input[type="number"], input[type="file"] {
    padding: 10px;
    width: 100%;
    margin: 10px 0;
    border: 1px solid #ccc;
}

h2 {
    margin-bottom: 20px;
}

.add-product-form-container {
    position: relative;
    background-color: #f9f9f9;
    padding: 20px;
    border-radius: 5px;
    margin-top: 20px;
    font-family: 'Goudy', sans-serif;
    font-weight: bold;
    gap: 20px; 
}

.add-product-form-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
}

.popup {
    display: none; 
    position: fixed;
    top: 50%;
    left: 58%;
    transform: translate(-50%, -50%);
    background-color: #fff;
    color: black;
    padding: 20px;
    border-radius: 10px;
    z-index: 1000; /* Modal above the overlay */
    font-family: 'Goudy', sans-serif;
    font-weight: bold;
    text-align: center;
}

#overlay {
    display: none; 
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.7); 
    z-index: 999; 
}

.close-btn {
    background-color: transparent;
    color: black;
    border: none;
    font-size: 24px;
    cursor: pointer;
    font-weight: bold;
    position: absolute; 
    top: 10px; 
    right: 20px; 
    z-index: 1001; 
}

.close-btn:hover {
    color: red; 
}

.right-margin-text {
    margin-left: 580px; 
    text-align: left;  
    margin-top: 200px;  
}

.product-image {
    max-width: 100px; 
    max-height: 100px; 
    object-fit: contain; 
    border: 1px solid #ccc; 
    padding: 5px;
}

.product-table {
    width: 120%;
    border-collapse: collapse; 
    margin-top: 20px;
    font-family: 'Goudy', sans-serif;
}

.product-table th, .product-table td {
    padding: 15px;
    text-align: center;
    border: 1px solid #ddd;
    font-size: 16px;
}

.product-table th {
    background-color: #1d1d1d;
    color: white;
    font-weight: bold;
}

.product-table tr:nth-child(even) {
    background-color: #f2f2f2;
}

.product-table tr:hover {
    background-color: #ddd;
}

.product-table td {
    font-size: 14px;
}

.product-table th:first-child {
    width: 15%; 
}

.product-table th:nth-child(2) {
    width: 20%; 
}

.product-table th:nth-child(3) {
    width: 25%; 
}

.product-table th:nth-child(4) {
    width: 20%; 
}

.product-table th:nth-child(5) {
    width: 20%; 
}

#addProductForm input[type="submit"] {
    background-color: #1d1d1d; 
    color: white; 
    padding: 10px 20px; 
    font-size: 16px; 
    border: none; 
    cursor: pointer; 
    border-radius: 5px; 
    transition: background-color 0.3s ease; 
    font-family: 'Goudy', sans-serif; 
    font-weight: bold; 
}

#addProductForm input[type="submit"]:hover {
    background-color: #b9b9b9;
    color: #1d1d1d 
}

.dashboard-stats {
    margin: 20px;
    font-family: Arial, sans-serif;
}

.stats-container {
    display: flex;
    justify-content: center;
    align-items: center; 
    gap: 40px; 
    margin-top: 30px; 
}

.stat-item {
    width: 200px; 
    height: 120px;
    background-color: #1d1d1d;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.956);
    text-align: center;
}

.stat-item h3 {
    margin-bottom: 10px;
    color: #ffffff;
}

.stat-item p {
    font-size: 20px;
    color: #ffffff;
    font-weight: bold;
}
.stats-container {
    display: flex;
    justify-content: center; 
    align-items: center; 
    gap: 40px; 
    margin-top: 30px; 
    margin-left: 268px;
}

.stat-item {
    text-align: center; 
}

.accounts-table {
    width: 120%;
    border-collapse: collapse;
    margin-top: 20px;
    font-family: 'Goudy', sans-serif;
}

.accounts-table th, .accounts-table td {
    padding: 15px;
    text-align: center;
    border: 1px solid #ddd;
    font-size: 16px;
}

.accounts-table th {
    background-color: #1d1d1d;
    color: white;
    font-weight: bold;
}

.accounts-table tr:nth-child(even) {
    background-color: #f2f2f2;
}

.accounts-table tr:hover {
    background-color: #ddd;
}

.delete-account-btn {
    background-color: #1D1D1D;
    color: white;
    padding: 8px 15px;
    border: none;
    cursor: pointer;
    border-radius: 5px;
    font-family: 'Arial', sans-serif;
    font-weight: bold;
    transition: background-color 0.3s ease;
}

.delete-account-btn:hover {
    background-color: #b9b9b9;
    color: #1d1d1d;
}

.delete-order-btn {
    background-color: #1D1D1D; 
    color: white; 
    padding: 8px 15px; 
    border: none; 
    cursor: pointer; 
    border-radius: 5px; 
    font-family: 'Arial', sans-serif; 
    font-weight: bold; 
    transition: background-color 0.3s ease; 
}

.delete-order-btn:hover {
    background-color: #b9b9b9; 
    color: #1d1d1d; 
}

.right-margin-text {
    margin-top: 200px;   
    margin-left: 578px;  
}
.content-section {
    display: none;
}
