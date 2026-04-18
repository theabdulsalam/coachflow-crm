<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'CoachFlow CRM') ?> | CoachFlow CRM</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Inter Font + Custom CSS -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="<?= BASE_URL ?>assets/css/style.css" rel="stylesheet">
</head>
<body data-base-url="<?= BASE_URL ?>"
      <?php if (!empty($flashMsg)): ?>
          data-flash="<?= htmlspecialchars($flashMsg) ?>"
          data-flash-type="<?= htmlspecialchars($flashType ?? 'success') ?>"
      <?php endif; ?>>
<div class="wrapper">
