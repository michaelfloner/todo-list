<?php

declare(strict_types=1);

namespace App\Security;

use App\Entity\Category;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class CategoryVoter extends Voter
{
    public const VIEW = 'view';
    public const DELETE = 'delete';

    protected function supports(string $attribute, mixed $subject): bool
    {
        if (!in_array($attribute, [self::VIEW, self::DELETE])) {
            return false;
        }

        if (!$subject instanceof Category) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        /** @var Category $category */
        $category = $subject;

        return match ($attribute) {
            self::VIEW, self::DELETE => $this->canEdit($category, $user),
            default => throw new \LogicException('This code should not be reached!')
        };
    }

    private function canEdit(Category $category, User $user): bool
    {
        return $user === $category->getOwner();
    }
}
