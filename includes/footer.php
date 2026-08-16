<?php /* Footer: closes layout wrapper, global JS, flush output buffer */ ?>

<?php if (isLoggedIn() && $page != 'login'): ?>
        </div>
    </div>
<?php endif; ?>

<script src="assets/js/app.js"></script>
<script>
console.log('MCR v3 - <?php echo $system_title; ?>');
</script>

</body>
</html>
