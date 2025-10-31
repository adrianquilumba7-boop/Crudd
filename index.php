<?php
require_once 'database.php';
$database = new Database();
$db = $database->getConnection();

$query = "SELECT * FROM usuarios ORDER BY fecha_creacion DESC";
$stmt = $db->prepare($query);
$stmt->execute();
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD Usuarios</title>
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
            padding: 40px 20px;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background-color: #1e1e1e;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
            overflow: hidden;
            border: 1px solid #333;
        }
        
        .header {
            background: linear-gradient(to right, #8B0000, #B22222);
            padding: 30px;
            text-align: center;
        }
        
        h1 {
            font-size: 32px;
            font-weight: 600;
            color: white;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.3);
        }
        
        .content {
            padding: 30px;
        }
        
        .alert {
            padding: 15px 20px;
            margin-bottom: 25px;
            border-radius: 6px;
            font-weight: 500;
            border-left: 4px solid;
        }
        
        .alert-success {
            background-color: rgba(0, 100, 0, 0.2);
            color: #90ee90;
            border-color: #32cd32;
        }
        
        .alert-error {
            background-color: rgba(139, 0, 0, 0.2);
            color: #ff6b6b;
            border-color: #B22222;
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
            display: inline-block;
            text-align: center;
        }
        
        .btn-primary {
            background: linear-gradient(to right, #8B0000, #B22222);
            color: white;
        }
        
        .btn-primary:hover {
            background: linear-gradient(to right, #9a0000, #c22a2a);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(178, 34, 34, 0.3);
        }
        
        .btn-warning {
            background: linear-gradient(to right, #ff8c00, #ffa500);
            color: white;
        }
        
        .btn-warning:hover {
            background: linear-gradient(to right, #ff9a00, #ffb52a);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 165, 0, 0.3);
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
        
        .table-container {
            margin-top: 30px;
            overflow-x: auto;
            border-radius: 8px;
            border: 1px solid #333;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #2a2a2a;
        }
        
        th {
            background: linear-gradient(to right, #8B0000, #B22222);
            color: white;
            padding: 15px;
            text-align: left;
            font-weight: 600;
            border-bottom: 2px solid #444;
        }
        
        td {
            padding: 15px;
            border-bottom: 1px solid #444;
            color: #e0e0e0;
        }
        
        tr:nth-child(even) {
            background-color: #252525;
        }
        
        tr:hover {
            background-color: #333;
            transition: background-color 0.3s ease;
        }
        
        .actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        
        .empty-state {
            text-align: center;
            padding: 40px;
            color: #888;
            font-size: 18px;
        }
        
        .empty-state i {
            font-size: 48px;
            margin-bottom: 15px;
            display: block;
            color: #555;
        }
        
        .footer {
            text-align: center;
            padding: 20px;
            color: #888;
            font-size: 14px;
            border-top: 1px solid #333;
            margin-top: 30px;
        }
        
        @media (max-width: 768px) {
            .container {
                border-radius: 0;
            }
            
            .header {
                padding: 20px;
            }
            
            h1 {
                font-size: 24px;
            }
            
            .content {
                padding: 20px;
            }
            
            th, td {
                padding: 10px;
            }
            
            .actions {
                flex-direction: column;
            }
            
            .btn {
                padding: 10px 15px;
                font-size: 14px;
            }
        }
        
        .btn i {
            margin-right: 8px;
        }
        
        .confirm-dialog {
            background-color: #2a2a2a;
            border: 1px solid #B22222;
            border-radius: 6px;
            padding: 15px;
            color: #f0f0f0;
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-users"></i> Gestión de Usuarios</h1>
        </div>
        
        <div class="content">
            <?php if (isset($_GET['mensaje'])): ?>
                <div class="alert alert-<?php echo $_GET['tipo'] ?? 'success'; ?>">
                    <i class="fas fa-<?php echo ($_GET['tipo'] ?? 'success') === 'success' ? 'check-circle' : 'exclamation-circle'; ?>"></i>
                    <?php echo htmlspecialchars($_GET['mensaje']); ?>
                </div>
            <?php endif; ?>
            
            <a href="create.php" class="btn btn-primary">
                <i class="fas fa-user-plus"></i> Nuevo Usuario
            </a>
            
            <?php if (count($usuarios) > 0): ?>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Email</th>
                                <th>Teléfono</th>
                                <th>Fecha de Creación</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($usuarios as $usuario): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($usuario['id']); ?></td>
                                <td><?php echo htmlspecialchars($usuario['nombre']); ?></td>
                                <td><?php echo htmlspecialchars($usuario['email']); ?></td>
                                <td><?php echo htmlspecialchars($usuario['telefono']); ?></td>
                                <td><?php echo htmlspecialchars($usuario['fecha_creacion']); ?></td>
                                <td>
                                    <div class="actions">
                                        <a href="edit.php?id=<?php echo $usuario['id']; ?>" class="btn btn-warning">
                                            <i class="fas fa-edit"></i> Editar
                                        </a>
                                        <a href="delete.php?id=<?php echo $usuario['id']; ?>" 
                                           class="btn btn-danger" 
                                           onclick="return confirm('¿Estás seguro de eliminar este usuario?')">
                                            <i class="fas fa-trash"></i> Eliminar
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-users-slash"></i>
                    <p>No hay usuarios registrados.</p>
                    <p style="margin-top: 10px; font-size: 14px;">Haz clic en "Nuevo Usuario" para agregar el primero.</p>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="footer">
            Sistema de Gestión de Usuarios &copy; <?php echo date('Y'); ?>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const deleteButtons = document.querySelectorAll('.btn-danger');
            
            deleteButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    if (!confirm('¿Estás seguro de eliminar este usuario?\nEsta acción no se puede deshacer.')) {
                        e.preventDefault();
                    }
                });
            });
        });
    </script>
</body>
</html>