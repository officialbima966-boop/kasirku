<?php
abstract class BaseModel {
    protected PDO $db;
    protected string $table;
    protected string $primary='id';
    public function __construct(){ $this->db=Database::connect(); }
    public function all(): array { return $this->db->query("SELECT * FROM {$this->table} ORDER BY {$this->primary} DESC")->fetchAll(); }
    public function find(int $id): ?array {
        $s=$this->db->prepare("SELECT * FROM {$this->table} WHERE {$this->primary}=?");
        $s->execute([$id]); return $s->fetch() ?: null;
    }
    public function delete(int $id): void {
        $s=$this->db->prepare("DELETE FROM {$this->table} WHERE {$this->primary}=?"); $s->execute([$id]);
    }
}
