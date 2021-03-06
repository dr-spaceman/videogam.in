<?php

namespace Vgsite;

use PDO;
use PDOStatement;

class BadgeMapper extends Mapper
{
    protected $db_table = 'badges';
    protected $db_id_field = 'badge_id';

    public function findAll(
        string $search = null,
        string $sort = null,
        int $limit_min = null,
        int $limit_max = null,
        array $input_parameters = []
    ): Collection {
        $sort = $sort ?? 'sort';
        return parent::findAll($search, $sort, $limit_min, $limit_max, $input_parameters);
    }

    public function findByName(string $name): ?DomainObject
    {
        $statement = $this->pdo->prepare("SELECT * FROM badges WHERE `name`=?");
        $statement->execute([$name]);
        $row = $statement->fetch();

        if (!is_array($row)) {
            return null;
        }

        return $this->createObject($row);
    }

    /**
     * Check/Get earned data
     * @param  int    $badge_id Badge ID
     * @param  int    $user_id  User ID
     * @return array|null           Array of table data or null if not found
     */
    public function findEarned(int $badge_id, int $user_id): ?array
    {
        $sql = "SELECT * FROM badges_earned WHERE badge_id=? AND user_id=? LIMIT 1";
        $statement = $this->pdo->prepare($sql);
        $statement->execute([$badge_id, $user_id]);
        $row = $statement->fetch();

        if (empty($row)) {
            return null;
        }

        return $row;
    }

    public function insertEarned(int $badge_id, int $user_id): bool
    {
        $sql = "INSERT INTO badges_earned (`badge_id`, `user_id`) VALUES (?, ?);";
        $statement = $this->pdo->prepare($sql);
        return $statement->execute([$badge_id, $user_id]);
    }

    protected function targetClass(): string
    {
        return Badge::class;
    }

    public function getCollection(array $rows): Collection
    {
        return new BadgeCollection($rows, $this);
    }

    protected function doCreateObject(array $row): DomainObject
    {
        $obj = new Badge(
            (int)($row['badge_id'] ?: $row[0]),
            $row['name'] ?: $row[1],
            $row['description'] ?: $row[2],
            (int)($row['value'] ?: $row[3]),
            (int)($row['rank'] ?: $row[4]),
        );

        return $obj;
    }

    protected function doInsert(DomainObject &$obj): DomainObject
    {
    }

    public function save(DomainObject $obj): bool
    {
    }

    public function delete(DomainObject $obj): bool
    {
    }

    public function markShown(Badge $badge, User $user)
    {
        $sql = "UPDATE badges_earned SET `new` = '0' WHERE badge_id=? AND user_id=? LIMIT 1";
        $statement = $this->pdo->prepare($sql);
        return $statement->execute($badge->getId(), $user->getId());
    }

    public function checkForNew(User $user): array
    {
        // check for new badges earned since last login
        // return array badge IDs
        $sql = "SELECT badge_id FROM badges_earned WHERE user_id=? AND `new`=1";
        $statement = $this->pdo->prepare($sql);
        $statement->execute([$user->getId()]);
        
        return $statement->fetchAll(PDO::FETCH_COLUMN);
    }
}