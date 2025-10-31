<?php
require_once 'database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $database = new Database();
    $db = $database->getConnection();
    
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
            $query = "INSERT INTO usuarios (nombre, email, telefono) VALUES (:nombre, :email, :telefono)";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':telefono', $telefono);
            
            if ($stmt->execute()) {
                header('Location: index.php?mensaje=Usuario creado correctamente&tipo=success');
                exit;
            }
        } catch(PDOException $e) {
            if ($e->getCode() == 23000) { 
                $errores[] = "El email ya está registrado";
            } else {
                $errores[] = "Error al crear el usuario: " . $e->getMessage();
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
    <title>Crear Usuario</title>
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
        }
        
        h1 {
            font-size: 28px;
            font-weight: 600;
            color: white;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.3);
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
        
        .required::after {
            content: " *";
            color: #B22222;
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
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Crear Nuevo Usuario</h1>
        </div>
        
        <div class="content">
            <?php if (!empty($errores)): ?>
                <div class="error-container">
                    <ul>
                        <?php foreach ($errores as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label for="nombre" class="required">Nombre</label>
                    <input type="text" id="nombre" name="nombre" value="<?php echo isset($_POST['nombre']) ? htmlspecialchars($_POST['nombre']) : ''; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="email" class="required">Email</label>
                    <input type="email" id="email" name="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="telefono">Teléfono</label>
                    <input type="text" id="telefono" name="telefono" value="<?php echo isset($_POST['telefono']) ? htmlspecialchars($_POST['telefono']) : ''; ?>">
                </div>
                
                <div class="btn-container">
                    <button type="submit" class="btn btn-primary">Guardar</button>
                    <a href="index.php" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
        
        <div class="footer">
            Sistema de Gestión de Usuarios
        </div>
    </div>
</body>
</html>