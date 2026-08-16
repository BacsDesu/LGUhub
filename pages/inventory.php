<?php /* Page: inventory — included by index.php when ?page=inventory */ ?>
    <!-- Inventory Section -->
    <div class="data-table">
        <div class="table-title">Current Stock Levels</div>
        <?php if(count($inventory) > 0): ?>
        <table>
            <thead>
                <tr><th>Product</th><th>Stock</th><th>Status</th><th>Update</th></tr>
            </thead>
            <tbody>
                <?php foreach($inventory as $inv): ?>
                <tr>
                    <td><?= htmlspecialchars($inv['product_name']) ?></td>
                    <td><strong><?= $inv['quantity'] ?></strong></td>
                    <td>
                        <?php if($inv['quantity'] < 10): ?>
                        <span class="status-low">⚠️ Low Stock</span>
                        <?php else: ?>
                        <span class="status-normal">✓ In Stock</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <form method="POST" class="inline-form">
                            <input type="hidden" name="action" value="update_inventory">
                            <input type="hidden" name="product_id" value="<?= $inv['product_id'] ?>">
                            <input type="number" name="quantity" value="<?= $inv['quantity'] ?>" class="inline-input">
                            <button type="submit" class="btn btn-sm">Update</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
        <div class="empty-state">No inventory data</div>
        <?php endif; ?>
    </div>
    
