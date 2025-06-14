# ✅ Testing Documentation

## 📦 Project: Inventory Management System
*Function Tested:* displayProduct , deleteOrder  
*File:* src/js/product.js  , order.js
*Tool Used:* Qodo Gen (in VS Code)

---

## 🧪 Purpose of Testing

The displayProduct function is responsible for rendering the list of products from the backend (XAMPP + MySQL) onto the front-end product management interface.

---

## 🔍 Testing Environment

- *Editor:* Visual Studio Code
- *Testing Tool:* Qodo Gen (Formerly CodiumAI)
- *Target File:* product.js, order.js
- *Target Function:* displayProduct(), deleteOrder()
- *Test Mode:* Static code analysis and runtime verification

---

## 🧠 Function Overview

```javascript
function displayProduct(products) {
  const tableBody = document.querySelector("#product-table tbody");
  tableBody.innerHTML = "";

  products.forEach((product) => {
    const row = document.createElement("tr");
    row.innerHTML = `
      <td>${product.id}</td>
      <td>${product.name}</td>
      <td>${product.category}</td>
      <td>${product.quantity}</td>
      <td>${product.price}</td>
      <td>
        <button onclick="editProduct(${product.id})">Edit</button>
        <button onclick="deleteProduct(${product.id})">Delete</button>
      </td>
    `;
    tableBody.appendChild(row);
  });
}
```
```
function deleteOrder(orderId) {
  if (confirm("Are you sure you want to delete this order?")) {
    fetch(delete_order.php?id=${orderId}, {
      method: "DELETE",
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          alert("Order deleted successfully!");
          loadOrders(); // Refresh order list
        } else {
          alert("Failed to delete order.");
        }
      })
      .catch((error) => {
        console.error("Error:", error);
        alert("An error occurred while deleting the order.");
      });
  }
}
```
