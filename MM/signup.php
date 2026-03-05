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
.t-container {
    margin-bottom: 10px;
}