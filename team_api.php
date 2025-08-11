<?php
header('Content-Type: application/json');
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

require_once 'config.php';

try {
    $pdo = getDBConnection();
} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed']);
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

switch ($method) {
    case 'GET':
        handleGet($pdo, $action);
        break;
    case 'POST':
        handlePost($pdo, $action);
        break;
    case 'PUT':
        handlePut($pdo, $action);
        break;
    case 'DELETE':
        handleDelete($pdo, $action);
        break;
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        break;
}

function handleGet($pdo, $action) {
    switch ($action) {
        case 'list':
            getTeamMembers($pdo);
            break;
        case 'member':
            $id = $_GET['id'] ?? 0;
            getTeamMember($pdo, $id);
            break;
        case 'stats':
            getTeamStats($pdo);
            break;
        default:
            http_response_code(400);
            echo json_encode(['error' => 'Invalid action']);
            break;
    }
}

function handlePost($pdo, $action) {
    switch ($action) {
        case 'add':
            addTeamMember($pdo);
            break;
        default:
            http_response_code(400);
            echo json_encode(['error' => 'Invalid action']);
            break;
    }
}

function handlePut($pdo, $action) {
    switch ($action) {
        case 'update':
            updateTeamMember($pdo);
            break;
        default:
            http_response_code(400);
            echo json_encode(['error' => 'Invalid action']);
            break;
    }
}

function handleDelete($pdo, $action) {
    switch ($action) {
        case 'delete':
            deleteTeamMember($pdo);
            break;
        default:
            http_response_code(400);
            echo json_encode(['error' => 'Invalid action']);
            break;
    }
}

function getTeamMembers($pdo) {
    try {
        $stmt = $pdo->query("SELECT * FROM team_members ORDER BY position, name");
        $members = $stmt->fetchAll();
        
        echo json_encode([
            'success' => true,
            'data' => $members,
            'count' => count($members)
        ]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to fetch team members']);
    }
}

function getTeamMember($pdo, $id) {
    if (!$id || !is_numeric($id)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid member ID']);
        return;
    }
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM team_members WHERE id = ?");
        $stmt->execute([$id]);
        $member = $stmt->fetch();
        
        if ($member) {
            echo json_encode([
                'success' => true,
                'data' => $member
            ]);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Team member not found']);
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to fetch team member']);
    }
}

function getTeamStats($pdo) {
    try {
        $totalMembers = $pdo->query("SELECT COUNT(*) FROM team_members")->fetchColumn();
        $withPhotos = $pdo->query("SELECT COUNT(*) FROM team_members WHERE avatar IS NOT NULL AND avatar != ''")->fetchColumn();
        $withLinkedIn = $pdo->query("SELECT COUNT(*) FROM team_members WHERE linkedin_profile IS NOT NULL AND linkedin_profile != ''")->fetchColumn();
        
        echo json_encode([
            'success' => true,
            'data' => [
                'total_members' => (int)$totalMembers,
                'with_photos' => (int)$withPhotos,
                'with_linkedin' => (int)$withLinkedIn
            ]
        ]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to fetch team stats']);
    }
}

function addTeamMember($pdo) {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid input data']);
        return;
    }
    
    $name = trim($input['name'] ?? '');
    $position = trim($input['position'] ?? '');
    $title = trim($input['title'] ?? '');
    $description = $input['description'] ?? '';
    $email = trim($input['email'] ?? '');
    $phone = trim($input['phone'] ?? '');
    $linkedin_profile = trim($input['linkedin_profile'] ?? '');
    $bio = trim($input['bio'] ?? '');
    
    if (empty($name) || empty($position)) {
        http_response_code(400);
        echo json_encode(['error' => 'Name and position are required']);
        return;
    }
    
    try {
        $stmt = $pdo->prepare("INSERT INTO team_members (name, position, title, description, email, phone, linkedin_profile, bio, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
        
        if ($stmt->execute([$name, $position, $title, $description, $email, $phone, $linkedin_profile, $bio])) {
            $id = $pdo->lastInsertId();
            
            echo json_encode([
                'success' => true,
                'message' => 'Team member added successfully',
                'data' => ['id' => $id]
            ]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to add team member']);
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
}

function updateTeamMember($pdo) {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid input data']);
        return;
    }
    
    $id = $input['id'] ?? 0;
    $name = trim($input['name'] ?? '');
    $position = trim($input['position'] ?? '');
    $title = trim($input['title'] ?? '');
    $description = $input['description'] ?? '';
    $email = trim($input['email'] ?? '');
    $phone = trim($input['phone'] ?? '');
    $linkedin_profile = trim($input['linkedin_profile'] ?? '');
    $bio = trim($input['bio'] ?? '');
    
    if (!$id || !is_numeric($id) || empty($name) || empty($position)) {
        http_response_code(400);
        echo json_encode(['error' => 'Valid ID, name and position are required']);
        return;
    }
    
    try {
        $stmt = $pdo->prepare("UPDATE team_members SET name=?, position=?, title=?, description=?, email=?, phone=?, linkedin_profile=?, bio=?, updated_at=NOW() WHERE id=?");
        
        if ($stmt->execute([$name, $position, $title, $description, $email, $phone, $linkedin_profile, $bio, $id])) {
            if ($stmt->rowCount() > 0) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Team member updated successfully'
                ]);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Team member not found or no changes made']);
            }
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to update team member']);
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
}

function deleteTeamMember($pdo) {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid input data']);
        return;
    }
    
    $id = $input['id'] ?? 0;
    
    if (!$id || !is_numeric($id)) {
        http_response_code(400);
        echo json_encode(['error' => 'Valid ID is required']);
        return;
    }
    
    try {
        // Get avatar path to delete file
        $stmt = $pdo->prepare("SELECT avatar FROM team_members WHERE id = ?");
        $stmt->execute([$id]);
        $member = $stmt->fetch();
        
        if ($member && $member['avatar'] && file_exists($member['avatar'])) {
            unlink($member['avatar']);
        }
        
        $stmt = $pdo->prepare("DELETE FROM team_members WHERE id = ?");
        
        if ($stmt->execute([$id])) {
            echo json_encode([
                'success' => true,
                'message' => 'Team member deleted successfully'
            ]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to delete team member']);
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
}
?>
