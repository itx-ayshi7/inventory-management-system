const { JSDOM } = require('jsdom');

global.orders = [];
global.displayOrders = jest.fn();

const { deleteOrder } = require('../js/order.js');

describe('deleteOrder', () => {
    beforeEach(() => {
        // Set up a fake DOM for displayOrders to use
        const dom = new JSDOM(`
            <table>
                <tbody id="order-table-body"></tbody>
            </table>
        `);
        global.document = dom.window.document;
        global.displayOrders.mockClear();
    });

    test('test_delete_existing_order', () => {
        global.orders = [
            { id: 1, customer: 'Alice', product: 'Widget', quantity: 2, total: 20 },
            { id: 2, customer: 'Bob', product: 'Gadget', quantity: 1, total: 15 }
        ];
        deleteOrder(1);
        expect(global.orders).toEqual([
            { id: 2, customer: 'Bob', product: 'Gadget', quantity: 1, total: 15 }
        ]);
    });

    test('test_display_orders_called_after_delete', () => {
        global.orders = [
            { id: 1, customer: 'Alice', product: 'Widget', quantity: 2, total: 20 }
        ];
        deleteOrder(1);
        expect(global.displayOrders).toHaveBeenCalled();
    });

    test('test_delete_nonexistent_order', () => {
        global.orders = [
            { id: 1, customer: 'Alice', product: 'Widget', quantity: 2, total: 20 }
        ];
        deleteOrder(999);
        expect(global.orders).toEqual([
            { id: 1, customer: 'Alice', product: 'Widget', quantity: 2, total: 20 }
        ]);
    });

    test('test_delete_from_empty_orders', () => {
        global.orders = [];
        expect(() => deleteOrder(1)).not.toThrow();
        expect(global.orders).toEqual([]);
    });

    test('test_delete_duplicate_order_ids', () => {
        global.orders = [
            { id: 1, customer: 'Alice', product: 'Widget', quantity: 2, total: 20 },
            { id: 1, customer: 'Bob', product: 'Gadget', quantity: 1, total: 15 },
            { id: 2, customer: 'Carol', product: 'Thing', quantity: 3, total: 30 }
        ];
        deleteOrder(1);
        expect(global.orders).toEqual([
            { id: 2, customer: 'Carol', product: 'Thing', quantity: 3, total: 30 }
        ]);
    });

    test('test_delete_with_invalid_id', () => {
        global.orders = [
            { id: 1, customer: 'Alice', product: 'Widget', quantity: 2, total: 20 }
        ];
        deleteOrder(undefined);
        expect(global.orders).toEqual([
            { id: 1, customer: 'Alice', product: 'Widget', quantity: 2, total: 20 }
        ]);
        deleteOrder(null);
        expect(global.orders).toEqual([
            { id: 1, customer: 'Alice', product: 'Widget', quantity: 2, total: 20 }
        ]);
    });
});