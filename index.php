<?php
$host = 'localhost'; $db = 'lead_db'; $user = 'root'; $pass = ''; 
try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'toggle_status') {
        $id = intval($_POST['lead_id']);
        $current_status = trim($_POST['current_status']);
        $new_status = (strcasecmp($current_status, 'Contacted') === 0) ? 'Not Contacted' : 'Contacted';
        $sql = "UPDATE statu SET status = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql); $stmt->execute([$new_status, $id]);
        header('Content-Type: application/json');
        echo json_encode(['status' => 'success', 'new_status' => $new_status]); exit;
    }
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_lead') {
        $id = intval($_POST['lead_id']);
        $sql = "DELETE FROM statu WHERE id = ?";
        $stmt = $pdo->prepare($sql); $stmt->execute([$id]);
        header('Content-Type: application/json');
        echo json_encode(['status' => 'success']); exit;
    }
    // if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create') {
    //     $topic = htmlspecialchars(trim($_POST['topic']));
    //     $company_name = htmlspecialchars(trim($_POST['company_name']));
    //     $vulnerability = htmlspecialchars(trim($_POST['vulnerability']));
    //     $your_solution = htmlspecialchars(trim($_POST['your_solution']));
    //     if (!empty($company_name) && !empty($your_solution)) {
    //         $sql = "INSERT INTO statu (topic, company_name, vulnerability, your_solution, status) VALUES (?, ?, ?, ?, 'Not Contacted')";
    //         $stmt = $pdo->prepare($sql); $stmt->execute([$topic, $company_name, $vulnerability, $your_solution]);
    //     }
    //     header("Location: index.php"); exit;
    // }



if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create') {
    $topic = htmlspecialchars(trim($_POST['topic']));
    $company_name = htmlspecialchars(trim($_POST['company_name']));
    
    // 1. ADD THESE TWO LINES TO READ THE INPUTS
    $email = htmlspecialchars(trim($_POST['email']));
    $phone = htmlspecialchars(trim($_POST['phone']));
    
    $vulnerability = htmlspecialchars(trim($_POST['vulnerability']));
    $your_solution = htmlspecialchars(trim($_POST['your_solution']));
    
    if (!empty($company_name) && !empty($your_solution)) {
        // 2. UPDATE THIS SQL TO INSERT EMAIL AND PHONE
        $sql = "INSERT INTO statu (topic, company_name, email, phone, vulnerability, your_solution, status) VALUES (?, ?, ?, ?, ?, ?, 'Not Contacted')";
        $stmt = $pdo->prepare($sql); $stmt->execute([$topic, $company_name, $email, $phone, $vulnerability, $your_solution]);
    }
    header("Location: index.php"); exit;
}










    $stmt = $pdo->query("SELECT * FROM statu ORDER BY id DESC");
    $leads = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) { die("Database connection failed: " . $e->getMessage()); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lead Tracker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
   <link href=" https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css"rel="stylesheet">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f8f9fa; margin: 40px; }
        .container { max-width: 1300px; margin: 0 auto; }
        h2, h3 { color: #2c3e50; margin-bottom: 20px; }
        .form-container { background: white; padding: 25px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); margin-bottom: 30px; }
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px; }
        .form-group { display: flex; flex-direction: column; }
        .form-group.full-width { grid-column: span 2; }
        label { font-weight: bold; margin-bottom: 5px; color: #4a5568; font-size: 14px; }
        input, select, textarea { padding: 10px; border: 1px solid #cbd5e0; border-radius: 5px; font-size: 15px; }
        textarea { resize: vertical; height: 70px; }
        .btn-submit { background-color: #38a169; color: white; border: none; padding: 12px 20px; font-size: 16px; font-weight: bold; border-radius: 5px; cursor: pointer; }
        .btn-submit:hover { background-color: #2f855a; }
        .search-box { width: 100%; padding: 12px; margin-bottom: 20px; box-sizing: border-box; border: 2px solid #cbd5e0; border-radius: 6px; font-size: 16px; }
        table { width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #edf2f7; vertical-align: middle; }
        th { background-color: #4a5568; color: white; text-transform: uppercase; font-size: 13px; letter-spacing: 0.5px; }
        tr { transition: opacity 0.3s ease, transform 0.3s ease; }
        tr:hover { background-color: #f7fafc; }
        .badge { background: #3182ce; color: white; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: bold; display: inline-block; }
        .problem { color: #e53e3e; }
        .solution { color: #38a169; font-weight: 600; }
        .status-btn { border: none; padding: 6px 12px; border-radius: 20px; font-weight: bold; font-size: 12px; cursor: pointer; transition: background 0.2s, color 0.2s; min-width: 120px; text-align: center; display: inline-block; }
        .status-not { background-color: #feebc8; color: #c05621; }
        .status-done { background-color: #c6f6d5; color: #22543d; }
        .btn-delete { background-color: #e53e3e; color: white; border: none; padding: 6px 12px; border-radius: 4px; font-size: 12px; font-weight: bold; cursor: pointer; display: inline-block; }
        .btn-delete:hover { background-color: #c53030; }
.phone {
    white-space: nowrap !important;
   
}

.phone-link {
    text-decoration: none;
       color:darkblue; 
}
     
.email-link{
       text-decoration: none;
       color:darkblue;
}


.email-btn{
    margin-top:5px;
    font-size:14px;
    padding:4px 10px;
}







    </style>
    <script>
    function toggleLeadStatus(buttonElement, leadId) {
        let currentStatus = buttonElement.innerText.trim();
        let formData = new FormData();
        formData.append('action', 'toggle_status');
        formData.append('lead_id', leadId);
        formData.append('current_status', currentStatus);
        fetch('index.php', { method: 'POST', body: formData })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                buttonElement.innerText = data.new_status;
                buttonElement.className = (data.new_status === 'Contacted') ? 'status-btn status-done' : 'status-btn status-not';
            }
        }).catch(error => console.error('Error:', error));
    }
    function deleteLeadInstantly(buttonElement, leadId) {
        if (!confirm('Are you sure you want to delete this lead?')) return;
        let formData = new FormData();
        formData.append('action', 'delete_lead');
        formData.append('lead_id', leadId);
        fetch('index.php', { method: 'POST', body: formData })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                let tableRow = buttonElement.closest('tr');
                tableRow.style.opacity = '0'; tableRow.style.transform = 'scale(0.95)';
                setTimeout(() => { tableRow.remove(); }, 300);
            }
        }).catch(error => console.error('Error:', error));
    }


document.addEventListener("DOMContentLoaded", function () {

    document.getElementById("searchInput").addEventListener("keyup", function () {
        let filter = this.value.toLowerCase();
        let rows = document.querySelectorAll("#leadsTable tr");

        rows.forEach(function(row) {
            let text = row.textContent.toLowerCase();
            row.style.display = text.includes(filter) ? "" : "none";
        });
    });

});



    
    </script>
</head>
<body>
<div class="container">
    <h2> Full-Stack PHP & MySQL Lead Tracker</h2>
    <div class="form-container">
        <h3>➕ Add a New Lead</h3>
        <form action="index.php" method="POST">
            <input type="hidden" name="action" value="create">
            <div class="form-grid">
                <div class="form-group">
                    <label for="topic">Topic / Category</label>
                    <select name="topic" id="topic" required>
                        <option value="E-Commerce (WooCommerce)">E-Commerce (WooCommerce)</option>
                        <option value="Local Business (WordPress)">Local Business (WordPress)</option>
                        <option value="SaaS Startup (Custom PHP)">SaaS Startup (Custom PHP)</option>
                        <option value="High-Traffic Blog (WordPress)">High-Traffic Blog (WordPress)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="company_name">Company Name</label>
                    <input type="text" name="company_name" id="company_name" required>
                </div>


<!-- Replace your Email and Phone HTML with this -->
<div class="form-group">
    <label for="email">Contact Email</label>
    <input type="email" name="email" id="email" required placeholder="name@company.com">
</div>
<div class="form-group">
    <label for="phone">Contact Phone</label>
    <input type="text" name="phone" id="phone" required placeholder="+92 300 1234567">
</div>












                <div class="form-group full-width">
                    <label for="vulnerability">The Vulnerability / Problem discovered</label>
                    <textarea name="vulnerability" id="vulnerability" required></textarea>
                </div>
                <div class="form-group full-width">
                    <label for="your_solution">Your Pitch / Code Solution</label>
                    <textarea name="your_solution" id="your_solution" required></textarea>
                </div>
            </div>
            <button type="submit" class="btn-submit" style="margin-top: 15px;">Save Lead to Database</button>
        </form>
    </div>
    <input type="text" id="searchInput" class="search-box" placeholder="Type to search companies or tech topics dynamically...">
    <table>
        <thead>
            <tr>
                <th>Topic</th>
                <th>Company Name</th>
                 <th>Email</th>
            <th>Phone</th>
                <th>The Vulnerability</th>
                <th>Your Solution</th>
                <th>Status (Click to Edit)</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody id="leadsTable">
            <?php foreach ($leads as $lead): ?>
                <tr>
                    <td><span class="badge"><?php echo htmlspecialchars($lead['topic']); ?></span></td>
                    <td><strong><?php echo htmlspecialchars($lead['company_name']); ?></strong></td>

<!-- Email Column -->
            <!-- <td>
                <a class="email-link" href="mailto:<?php echo htmlspecialchars($lead['email']); ?>">
                <?php echo htmlspecialchars($lead['email']); ?>
            </a>
            </td> -->
               
<!-- <td>
    <a class="email-link"
       href="mailto:<?php echo htmlspecialchars($lead['email']); ?>">
        <?php echo htmlspecialchars($lead['email']); ?>
    </a>
</td> -->

<!-- <td>
    <a target="_blank"
       href="https://mail.google.com/mail/?view=cm&fs=1&to=<?php echo urlencode($lead['email']); ?>">
        <?php echo htmlspecialchars($lead['email']); ?>
    </a>
</td> -->

<td class="email-link" id="phoneTableCell">
    <?php echo htmlspecialchars($lead['email']); ?>
    <br>
    <a class="btn btn-sm btn-primary mt-1 email-btn"
       target="_blank"
       href="https://mail.google.com/mail/?view=cm&fs=1&to=<?php echo urlencode($lead['email']); ?>">
        Send Email
    </a>
</td>












            <!-- Phone Column -->
            <td id="emailTableCell" class="phone">
                  <a class="phone-link" href="tel:<?php echo htmlspecialchars($lead['phone']); ?>">
                <?php echo htmlspecialchars($lead['phone']); ?>
            </a>
            </td>
           
                    <td class="problem"><?php echo htmlspecialchars($lead['vulnerability']); ?></td>
                    <td class="solution"><?php echo htmlspecialchars($lead['your_solution']); ?></td>
                    <td>
                        <button type="button" class="status-btn <?php echo (strcasecmp(trim($lead['status']), 'Contacted') === 0) ? 'status-done' : 'status-not'; ?>" onclick="toggleLeadStatus(this, <?php echo $lead['id']; ?>)">
                            <?php echo htmlspecialchars(!empty($lead['status']) ? trim($lead['status']) : 'Not Contacted'); ?>
                        </button>
                    </td>
                    <td>
                        <button type="button" class="btn-delete" onclick="deleteLeadInstantly(this, <?php echo $lead['id']; ?>)">Delete</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
            <!-- <footer >
          <div class="text-center mt-4 fw-bold" >
            <p class="mb-0">&copy; 2026 lead tracker. All Rights Reserved.Made by Maheen Ghaffar</p>
        </div>        
        
        </footer> -->

        <!-- <footer class="bg-dark text-white py-4 mt-5">
  <div class="container text-center">

    <
    <h5 class="mb-3">Contact Us</h5>

    <p class="mb-1">Email: support@leadtracker.com</p>
    <p class="mb-1">Phone: +92 300 1234567</p>

 

      <div class="col-md-4 mb-4">
        <h5 class="fw-bold mb-3">Follow Us</h5>
        <a href="#" class="text-white fs-5 me-3 us"><i class="fab fa-facebook-f"></i></a>
        <a href="#" class="text-white fs-5 me-3 us"><i class="fab fa-instagram"></i></a>
        <a href="#" class="text-white fs-5 me-3 us"><i class="fab fa-twitter"></i></a>
        <a href="#" class="text-white fs-5 us"><i class="fab fa-youtube"></i></a>
      </div>

    </div>
   
    <p class="mb-0 fw-bold">
      &copy; 2026 Lead Tracker. All Rights Reserved. Made by Maheen Ghaffar
    </p>

  </div>
</footer> -->


<footer  style="background-color: #3c4855 "  class=" text-white py-4 mt-5">
  <div class="container text-center">

    <!-- Contact Info -->
    <h5 class="mb-3">Contact Us</h5>

    <p class="mb-1">Email:maheenghaffar92@gmail.com</p>
    <p class="mb-3">Phone: +92 321 3937954</p>

    <!-- Social Links -->
    <div class="mb-3">
      <h6 class="fw-bold mb-2">Follow Us</h6>

      <a href="#" class="text-white fs-5 me-3 us">
        <i class="fab fa-facebook-f"></i>
      </a>

      <a href="#" class="text-white fs-5 me-3 us">
        <i class="fab fa-instagram"></i>
      </a>

      <a href="#" class="text-white fs-5 me-3 us">
        <i class="fab fa-twitter"></i>
      </a>

      <a href="#" class="text-white fs-5 us">
        <i class="fab fa-youtube"></i>
      </a>
    </div>

    <!-- Copyright -->
    <p class="mb-0 fw-bold">
      &copy; 2026 Lead Tracker. All Rights Reserved. Made by Maheen Ghaffar
    </p>

  </div>
</footer>
<script>
    // Get the HTML elements
    const emailInput = document.getElementById('emailFormInput');
    const phoneInput = document.getElementById('phoneFormInput');
    
    const emailCell = document.getElementById('emailTableCell');
    const phoneCell = document.getElementById('phoneTableCell');

    // Update email cell on type
    emailInput.addEventListener('input', function() {
        emailCell.textContent = this.value || '-';
    });

    // Update phone cell on type
    phoneInput.addEventListener('input', function() {
        phoneCell.textContent = this.value || '-';
    });
</script>
