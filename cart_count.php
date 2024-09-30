<?php
session_start();
echo $_SESSION['cart_count'] ?? 0;
