<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../jwt.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }

$method = $_SERVER['REQUEST_METHOD'];
$id     = isset($_GET['id']) ? (int)$_GET['id'] : null;

try {
    $db = getDB();

    if ($method === 'GET') {
        if ($id) {
            $blog = $db->fetchOne("SELECT * FROM blogs WHERE id = ?", [$id]);
            if (!$blog) sendError('Not found', 404);
            $blog['tags'] = json_decode($blog['tags'] ?? '[]', true) ?: [];
            sendJson($blog);
        } else {
            $status = $_GET['status'] ?? '';
            $sql    = "SELECT * FROM blogs";
            $params = [];
            if ($status) { $sql .= " WHERE status = ?"; $params[] = $status; }
            $sql .= " ORDER BY created_at DESC";
            $blogs = $db->fetchAll($sql, $params);
            foreach ($blogs as &$b) $b['tags'] = json_decode($b['tags'] ?? '[]', true) ?: [];
            sendJson($blogs);
        }
    }

    elseif ($method === 'POST') {
        requireAdmin();
        $data = getJsonInput();
        if (empty($data['title']) || empty($data['content'])) sendError('Title and content required', 400);

        $newId = $db->insert('blogs', [
            'title'            => sanitize($data['title']),
            'content'          => $data['content'], // allow HTML
            'author'           => sanitize($data['author'] ?? 'Admin'),
            'image'            => sanitize($data['image'] ?? ''),
            'status'           => in_array($data['status'] ?? '', ['published','draft']) ? $data['status'] : 'published',
            'category'         => sanitize($data['category'] ?? 'General'),
            'read_time'        => sanitize($data['read_time'] ?? ''),
            'tags'             => json_encode(is_array($data['tags'] ?? null) ? $data['tags'] : []),
            'meta_description' => sanitize($data['meta_description'] ?? ''),
        ]);

        $blog = $db->fetchOne("SELECT * FROM blogs WHERE id = ?", [$newId]);
        $blog['tags'] = json_decode($blog['tags'] ?? '[]', true) ?: [];
        sendJson($blog, 201);
    }

    elseif ($method === 'PUT' && $id) {
        requireAdmin();
        $data = getJsonInput();
        $existing = $db->fetchOne("SELECT * FROM blogs WHERE id = ?", [$id]);
        if (!$existing) sendError('Not found', 404);

        $db->update('blogs', [
            'title'            => sanitize($data['title'] ?? $existing['title']),
            'content'          => $data['content'] ?? $existing['content'],
            'author'           => sanitize($data['author'] ?? $existing['author']),
            'image'            => sanitize($data['image'] ?? $existing['image']),
            'status'           => in_array($data['status'] ?? '', ['published','draft']) ? $data['status'] : $existing['status'],
            'category'         => sanitize($data['category'] ?? $existing['category']),
            'read_time'        => sanitize($data['read_time'] ?? $existing['read_time']),
            'tags'             => json_encode(is_array($data['tags'] ?? null) ? $data['tags'] : (json_decode($existing['tags'] ?? '[]', true) ?: [])),
            'meta_description' => sanitize($data['meta_description'] ?? $existing['meta_description']),
            'updated_at'       => date('Y-m-d H:i:s'),
        ], 'id = :id', [':id' => $id]);

        $blog = $db->fetchOne("SELECT * FROM blogs WHERE id = ?", [$id]);
        $blog['tags'] = json_decode($blog['tags'] ?? '[]', true) ?: [];
        sendJson($blog);
    }

    elseif ($method === 'DELETE' && $id) {
        requireAdmin();
        $db->delete('blogs', 'id = ?', [$id]);
        sendJson(['success' => true]);
    }

    else {
        sendError('Not found', 404);
    }

} catch (Exception $e) {
    error_log('Blogs error: ' . $e->getMessage());
    sendError('Server error', 500);
}
