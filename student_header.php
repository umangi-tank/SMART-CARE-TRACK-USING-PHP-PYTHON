    <div class="dashboard-header">
        <div>
            <h2>Welcome, <?php echo htmlspecialchars($_SESSION['user_email']); ?></h2>
            <p class="text-muted">Here’s an overview of your activities</p>
        </div>
        <form method="post" action="index.php">
            <button type="submit" class="logout-btn">Logout</button>
        </form>
    </div>
