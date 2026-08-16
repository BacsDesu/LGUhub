<?php /* Page: transactions — included by index.php when ?page=transactions */ ?>
    <!-- Transactions Section -->
    <div class="data-table">
        <div class="table-title">Transaction History</div>
        <?php if(count($transactions) > 0): ?>
        <table>
            <thead>
                <tr><th>ID</th><th>Product</th><th>Type</th><th>Qty</th><th>Date</th></tr>
            </thead>
            <tbody>
                <?php foreach($transactions as $t): ?>
                <tr>
                    <td><?= $t['transaction_id'] ?></td>
                    <td><?= htmlspecialchars($t['product_name']) ?></td>
                    <td><span class="badge <?= $t['transaction_type'] == 'IN' ? 'badge-in' : 'badge-out' ?>"><?= $t['transaction_type'] ?></span></td>
                    <td><?= $t['quantity'] ?></td>
                    <td><?= date('Y-m-d H:i', strtotime($t['transaction_date'])) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
        <div class="empty-state">No transactions</div>
        <?php endif; ?>
    </div>
    
    <div class="form-card">
        <h3>Record Stock Movement</h3>
        <form method="POST">
            <input type="hidden" name="action" value="add_transaction">
            <div class="form-group">
                <label>Product</label>
                <select name="product_id" required>
                    <option value="">Select Product</option>
                    <?php foreach($products as $p): ?>
                    <option value="<?= $p['product_id'] ?>"><?= htmlspecialchars($p['product_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Type</label>
                <select name="transaction_type" required>
                    <option value="IN">IN (Restock)</option>
                    <option value="OUT">OUT (Sale)</option>
                </select>
            </div>
            <div class="form-group">
                <label>Quantity</label>
                <input type="number" name="quantity" min="1" required>
            </div>
            <button type="submit" class="btn btn-primary">Record Transaction</button>
        </form>
    </div>
    
