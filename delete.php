<?php
require_once 'database.php';

$database = new Database();
$db = $database->getConnection();

$id = $_GET['id'] ?? null;

if (!$id) {
    header('Location: index.php?mensaje=ID de usuario no válido&tipo=error');
    exit;
}

$query = "SELECT * FROM usuarios WHERE id = :id";
$stmt = $db->prepare($query);
$stmt->bindParam(':id', $id);
$stmt->execute();
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
    header('Location: index.php?mensaje=Usuario no encontrado&tipo=error');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $query = "DELETE FROM usuarios WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $id);
        
        if ($stmt->execute()) {
            header('Location: index.php?mensaje=Usuario eliminado correctamente&tipo=success');
            exit;
        } else {
            $error = "Error al eliminar el usuario";
        }
    } catch(PDOException $e) {
        $error = "Error al eliminar el usuario: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eliminar Usuario</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #1a1a1a 0%, #0a0a0a 100%);
            color: #f0f0f0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .container {
            width: 100%;
            max-width: 500px;
            background-color: #1e1e1e;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
            overflow: hidden;
            border: 1px solid #333;
            animation: slideIn 0.3s ease-out;
        }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .header {
            background: linear-gradient(to right, #8B0000, #B22222);
            padding: 25px 30px;
            text-align: center;
        }
        
        h1 {
            font-size: 28px;
            font-weight: 600;
            color: white;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.3);
        }
        
        .warning-icon {
            font-size: 48px;
            color: #ff6b6b;
            margin-bottom: 15px;
        }
        
        .content {
            padding: 30px;
            text-align: center;
        }
        
        .user-info {
            background: rgba(139, 0, 0, 0.1);
            border: 1px solid #333;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            text-align: left;
        }
        
        .user-detail {
            margin: 10px 0;
            padding: 8px 0;
            border-bottom: 1px solid #333;
        }
        
        .user-detail:last-child {
            border-bottom: none;
        }
        
        .detail-label {
            font-weight: 600;
            color: #ccc;
            display: inline-block;
            width: 120px;
        }
        
        .detail-value {
            color: #f0f0f0;
        }
        
        .warning-message {
            background: rgba(139, 0, 0, 0.2);
            border-left: 4px solid #B22222;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
            text-align: left;
        }
        
        .btn-container {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }
        
        .btn {
            padding: 12px 25px;
            text-decoration: none;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            font-size: 16px;
            font-weight: 500;
            transition: all 0.3s ease;
            flex: 1;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        
        .btn-danger {
            background: linear-gradient(to right, #8b0000, #dc143c);
            color: white;
        }
        
        .btn-danger:hover {
            background: linear-gradient(to right, #9a0000, #e6143c);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(220, 20, 60, 0.3);
        }
        
        .btn-secondary {
            background-color: #333;
            color: #e0e0e0;
            border: 1px solid #555;
        }
        
        .btn-secondary:hover {
            background-color: #444;
            transform: translateY(-2px);
        }
        
        .error-message {
            background-color: rgba(139, 0, 0, 0.2);
            border-left: 4px solid #B22222;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
            color: #ff6b6b;
        }
        
        .footer {
            text-align: center;
            padding: 20px;
            color: #888;
            font-size: 14px;
            border-top: 1px solid #333;
            margin-top: 20px;
        }
        
        @media (max-width: 600px) {
            .btn-container {
                flex-direction: column;
            }
            
            .header {
                padding: 20px;
            }
            
            h1 {
                font-size: 24px;
            }
            
            .detail-label {
                width: 100px;
            }
        }
        
        .pulse {
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
            }
            100% {
                transform: scale(1);
            }
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-trash-alt"></i> Eliminar Usuario</h1>
        </div>
        
        <div class="content">
            <div class="warning-icon pulse">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            
            <h2 style="color: #ff6b6b; margin-bottom: 20px;">¿Está seguro de eliminar este usuario?</h2>
            
            <?php if (isset($error)): ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <div class="user-info">
                <div class="user-detail">
                    <span class="detail-label">ID:</span>
                    <span class="detail-value"><?php echo htmlspecialchars($usuario['id']); ?></span>
                </div>
                <div class="user-detail">
                    <span class="detail-label">Nombre:</span>
                    <span class="detail-value"><?php echo htmlspecialchars($usuario['nombre']); ?></span>
                </div>
                <div class="user-detail">
                    <span class="detail-label">Email:</span>
                    <span class="detail-value"><?php echo htmlspecialchars($usuario['email']); ?></span>
                </div>
                <div class="user-detail">
                    <span class="detail-label">Teléfono:</span>
                    <span class="detail-value"><?php echo htmlspecialchars($usuario['telefono'] ?: 'No especificado'); ?></span>
                </div>
                <div class="user-detail">
                    <span class="detail-label">Fecha creación:</span>
                    <span class="detail-value"><?php echo htmlspecialchars($usuario['fecha_creacion']); ?></span>
                </div>
            </div>
            
            <div class="warning-message">
                <i class="fas fa-exclamation-circle"></i>
                <strong>Advertencia:</strong> Esta acción no se puede deshacer. Todos los datos del usuario serán eliminados permanentemente del sistema.
            </div>
            
            <form method="POST">
                <div class="btn-container">
                    <button type="submit" class="btn btn-danger" onclick="return confirm('¿Está completamente seguro? Esta acción es irreversible.')">
                        <i class="fas fa-trash"></i> Confirmar Eliminación
                    </button>
                    <a href="index.php" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
        
        <div class="footer">
            Sistema de Gestión de Usuarios
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const deleteForm = document.querySelector('form');
            const deleteButton = document.querySelector('.btn-danger');
            
            deleteButton.addEventListener('click', function(e) {
                if (!confirm('⚠️ ADVERTENCIA FINAL ⚠️\n\nEstá a punto de eliminar permanentemente al usuario:\n\n' +
                           '• ' + document.querySelector('.user-detail:nth-child(2) .detail-value').textContent + '\n' +
                           '• ' + document.querySelector('.user-detail:nth-child(3) .detail-value').textContent + '\n\n' +
                           '¿Está ABSOLUTAMENTE seguro de continuar?')) {
                    e.preventDefault();
                }
            });
        });
    </script>
</body>
</html>