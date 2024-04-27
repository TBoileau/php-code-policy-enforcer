<?php

declare(strict_types=1);

namespace TBoileau\PhpCodePolicyEnforcer\Exception;

use SplFileInfo;

final class ClassMapperException extends CodePolicyEnforcerException
{
    public static function directoryDoesNotExist(string $directory): self
    {
        return new self(
            sprintf('"%s" directory does not exist.', $directory),
            ['directory' => $directory]
        );
    }

    public static function fileIsNotClass(SplFileInfo $file): self
    {
        return new self(
            sprintf('"%s" is not a class.', $file->getFilename()),
            ['file' => $file]
        );
    }
}
