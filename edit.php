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
    $nombre = trim($_POST['nombre']);
    $email = trim($_POST['email']);
    $telefono = trim($_POST['telefono']);
    
    $errores = [];
    
    if (empty($nombre)) {
        $errores[] = "El nombre es obligatorio";
    }
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errores[] = "El email no es válido";
    }
    
    if (empty($errores)) {
        try {
            $query = "UPDATE usuarios SET nombre = :nombre, email = :email, telefono = :telefono WHERE id = :id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':telefono', $telefono);
            $stmt->bindParam(':id', $id);
            
            if ($stmt->execute()) {
                header('Location: index.php?mensaje=Usuario actualizado correctamente&tipo=success');
                exit;
            }
        } catch(PDOException $e) {
            if ($e->getCode() == 23000) { // Código de violación de unicidad
                $errores[] = "El email ya está registrado en otro usuario";
            } else {
                $errores[] = "Error al actualizar el usuario: " . $e->getMessage();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuario</title>
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
            padding: 40px 20px;
        }
        
        .container {
            width: 100%;
            max-width: 600px;
            background-color: #1e1e1e;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
            overflow: hidden;
            border: 1px solid #333;
        }
        
        .header {
            background: linear-gradient(to right, #8B0000, #B22222);
            padding: 25px 30px;
            text-align: center;
            position: relative;
        }
        
        h1 {
            font-size: 28px;
            font-weight: 600;
            color: white;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.3);
        }
        
        .user-id {
            position: absolute;
            top: 15px;
            right: 20px;
            background: rgba(0, 0, 0, 0.3);
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 14px;
            color: #f0f0f0;
        }
        
        .content {
            padding: 30px;
        }
        
        .error-container {
            background-color: rgba(139, 0, 0, 0.2);
            border-left: 4px solid #B22222;
            padding: 15px;
            margin-bottom: 25px;
            border-radius: 4px;
        }
        
        .error-container ul {
            list-style-type: none;
        }
        
        .error-container li {
            padding: 5px 0;
            color: #ff6b6b;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #e0e0e0;
        }
        
        .required::after {
            content: " *";
            color: #B22222;
        }
        
        input[type="text"], input[type="email"] {
            width: 100%;
            padding: 12px 15px;
            background-color: #2a2a2a;
            border: 1px solid #444;
            border-radius: 6px;
            color: #f0f0f0;
            font-size: 16px;
            transition: all 0.3s ease;
        }
        
        input[type="text"]:focus, input[type="email"]:focus {
            outline: none;
            border-color: #B22222;
            box-shadow: 0 0 0 2px rgba(178, 34, 34, 0.2);
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
        
        .btn-primary {
            background: linear-gradient(to right, #8B0000, #B22222);
            color: white;
        }
        
        .btn-primary:hover {
            background: linear-gradient(to right, #9a0000, #c22a2a);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(178, 34, 34, 0.3);
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
        
        .user-info {
            background: rgba(139, 0, 0, 0.1);
            border: 1px solid #333;
            border-radius: 6px;
            padding: 15px;
            margin-bottom: 20px;
        }
        
        .user-info p {
            margin: 5px 0;
            color: #ccc;
            font-size: 14px;
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
            
            .user-id {
                position: static;
                display: inline-block;
                margin-top: 10px;
            }
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-user-edit"></i> Editar Usuario</h1>
            <div class="user-id">ID: <?php echo htmlspecialchars($usuario['id']); ?></div>
        </div>
        
        <div class="content">
            <div class="user-info">
                <p><i class="fas fa-calendar"></i> <strong>Fecha de creación:</strong> <?php echo htmlspecialchars($usuario['fecha_creacion']); ?></p>
                <p><i class="fas fa-clock"></i> <strong>Última actualización:</strong> <?php echo htmlspecialchars($usuario['fecha_actualizacion'] ?? 'No disponible'); ?></p>
            </div>
            
            <?php if (!empty($errores)): ?>
                <div class="error-container">
                    <ul>
                        <?php foreach ($errores as $error): ?>
                            <li><i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label for="nombre" class="required">Nombre</label>
                    <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($usuario['nombre']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="email" class="required">Email</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($usuario['email']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="telefono">Teléfono</label>
                    <input type="text" id="telefono" name="telefono" value="<?php echo htmlspecialchars($usuario['telefono']); ?>">
                </div>
                
                <div class="btn-container">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Actualizar
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
</body>
</html>