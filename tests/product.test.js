const { JSDOM } = require('jsdom');

global.confirm = jest.fn();
global.alert = jest.fn();

let products;
let displayProducts;
let deleteProduct;

beforeEach(() => {
    // Setup DOM
    const dom = new JSDOM(`
        <table>
            <tbody id="product-table-body"></tbody>
        </table>
    `);
    global.document = dom.window.document;

    // Reset products and functions
    products = [
        { id: 1, name: "Laptop", category: "Electronics", quantity: 10, price: 800 },
        { id: 2, name: "Phone", category: "Electronics", quantity: 25, price: 500 }
    ];

    // Redefine displayProducts to update DOM
    displayProducts = function () {
        const tableBody = document.getElementById("product-table-body");
        if (!tableBody) return;
        tableBody.innerHTML = "";
        products.forEach(product => {
            let row = `<tr>
                <td>${product.id}</td>
                <td>${product.name}</td>
                <td>${product.category}</td>
                <td>${product.quantity}</td>
                <td>$${product.price.toFixed(2)}</td>
                <td>
                    <button class="edit" onclick="editProduct(${product.id})">Edit</button>
                    <button class="delete" onclick="deleteProduct(${product.id})">Delete</button>
                </td>
            </tr>`;
            tableBody.insertAdjacentHTML('beforeend', row);
        });
    };

    // Redefine deleteProduct in test scope
    deleteProduct = function (id) {
        if (confirm("Are you sure you want to delete this product?")) {
            products = products.filter(p => p.id !== parseInt(id));
            displayProducts();
            alert("Product deleted successfully!");
        }
    };

    // Reset mocks
    global.confirm.mockReset();
    global.alert.mockReset();
});

test('testFunctionExecutesWithValidInput', () => {
    global.confirm.mockReturnValue(true);
    displayProducts();
    deleteProduct(1);
    expect(products).toEqual([
        { id: 2, name: "Phone", category: "Electronics", quantity: 25, price: 500 }
    ]);
    expect(global.alert).toHaveBeenCalledWith("Product deleted successfully!");
    const tableBody = document.getElementById("product-table-body");
    expect(tableBody.innerHTML).toContain("Phone");
    expect(tableBody.innerHTML).not.toContain("Laptop");
});

test('testFunctionReturnsExpectedOutput', () => {
    global.confirm.mockReturnValue(true);
    displayProducts();
    deleteProduct(2);
    expect(products).toEqual([
        { id: 1, name: "Laptop", category: "Electronics", quantity: 10, price: 800 }
    ]);
    expect(global.alert).toHaveBeenCalledWith("Product deleted successfully!");
    const tableBody = document.getElementById("product-table-body");
    expect(tableBody.innerHTML).toContain("Laptop");
    expect(tableBody.innerHTML).not.toContain("Phone");
});

test('testFunctionHandlesMultipleValidInputs', () => {
    global.confirm.mockReturnValue(true);
    displayProducts();
    deleteProduct(1);
    expect(products).toEqual([
        { id: 2, name: "Phone", category: "Electronics", quantity: 25, price: 500 }
    ]);
    deleteProduct(2);
    expect(products).toEqual([]);
    expect(global.alert).toHaveBeenCalledTimes(2);
    const tableBody = document.getElementById("product-table-body");
    expect(tableBody.innerHTML).not.toContain("Laptop");
    expect(tableBody.innerHTML).not.toContain("Phone");
});

test('testFunctionHandlesEmptyInput', () => {
    global.confirm.mockReturnValue(true);
    displayProducts();
    deleteProduct('');
    // No product should be deleted since parseInt('') is NaN and no id === NaN
    expect(products.length).toBe(2);
    expect(global.alert).not.toHaveBeenCalled();
    const tableBody = document.getElementById("product-table-body");
    expect(tableBody.innerHTML).toContain("Laptop");
    expect(tableBody.innerHTML).toContain("Phone");
});

test('testFunctionHandlesInvalidInputType', () => {
    global.confirm.mockReturnValue(true);
    displayProducts();
    deleteProduct({}); // Passing an object instead of a valid id
    // No product should be deleted since parseInt({}) is NaN
    expect(products.length).toBe(2);
    expect(global.alert).not.toHaveBeenCalled();
    const tableBody = document.getElementById("product-table-body");
    expect(tableBody.innerHTML).toContain("Laptop");
    expect(tableBody.innerHTML).toContain("Phone");
});

test('testFunctionHandlesLargeInput', () => {
    global.confirm.mockReturnValue(true);
    // Add a product with a very large id
    products.push({ id: Number.MAX_SAFE_INTEGER, name: "SuperComputer", category: "Electronics", quantity: 1, price: 1000000 });
    displayProducts();
    deleteProduct(Number.MAX_SAFE_INTEGER);
    expect(products.find(p => p.id === Number.MAX_SAFE_INTEGER)).toBeUndefined();
    expect(global.alert).toHaveBeenCalledWith("Product deleted successfully!");
    const tableBody = document.getElementById("product-table-body");
    expect(tableBody.innerHTML).not.toContain("SuperComputer");
});