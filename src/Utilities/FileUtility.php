<?php namespace DCarbone\AmberHat\Utilities;

/**
 * Class FileUtility
 * @package DCarbone\AmberHat\Utilities
 */
abstract class FileUtility
{
    /**
     * @param string $tmpFilename
     * @return array
     */
    public static function extractHeadersFromFile($tmpFilename)
    {
        if (!file_exists($tmpFilename))
        {
            throw new \InvalidArgumentException(sprintf(
                '%s::extractHeadersFromFile - Specified non existent file "%s".',
                get_called_class(),
                $tmpFilename
            ));
        }

        $fh = fopen($tmpFilename, 'c+b');

        if ($fh)
        {
            $headers = array();

            // Default apache header size is 8KB, hopefully OK for ALL TIME.
            $lineNum = 0;
            $rns = 0;
            $headerNum = 0;
            $possibleHeader = array();
            $innerHeaderLineCount = 0;
            $byteOffset = 0;
            while (false !== ($line = fgets($fh, 8192)))
            {
                // If we do not have headers in the output...
                if (0 === $lineNum && strpos($line, 'HTTP/1.') !== 0)
                {
                    $headers = null;
                    $byteOffset = null;
                    break;
                }

                // We...probably should just give up.
                if ($innerHeaderLineCount > 100)
                {
                    $headers = null;
                    $byteOffset = null;
                    break;
                }

                // If we hit here, we're probably done with parsing headers...
                if ($headerNum > 0 && $line !== "\r\n" && strpos($line, 'HTTP/1.') !== 0)
                    break;

                $byteOffset = ftell($fh);

                if (($rns === 0 && substr($line, -2) === "\r\n") || $line === "\r\n")
                    $rns++;

                if ($rns === 2)
                {
                    $headers[$headerNum] = $possibleHeader;
                    $headerNum++;
                    $innerHeaderLineCount = 0;
                    $rns = 0;
                    continue;
                }

                if (strpos($line, ':') === false)
                {
                    $possibleHeader[] = trim($line);
                }
                else
                {
                    list ($header, $value) = explode(':', $line, 2);
                    $possibleHeader[trim($header)] = trim($value);
                }

                $lineNum++;
                $innerHeaderLineCount++;
            }

            fclose($fh);

            return array($headers, $byteOffset);
        }

        throw new \RuntimeException(sprintf(
            '%s::extractHeadersFromFile - Unable to open file "%s" for reading & writing.',
            get_called_class(),
            $tmpFilename
        ));
    }

    /**
     * @param string $tmpFilename
     * @param int $contentStartOffset
     * @param string $outputDir
     * @param string $filename
     * @return string
     */
    public static function removeHeadersAndMoveFile($tmpFilename, $contentStartOffset, $outputDir, $filename)
    {
        $tfh = fopen($tmpFilename, 'c+b');
        if ($tfh)
        {
            $outputFile = sprintf('%s/%s', rtrim($outputDir, "/\\"), $filename);

            $fh = fopen($outputFile, 'w+b');

            if ($fh)
            {
                fseek($tfh, $contentStartOffset);
                while (false === feof($tfh) && false !== ($data = fread($tfh, 8192)))
                {
                    fwrite($fh, $data);
                }

                fclose($tfh);
                fclose($fh);

                if (false === (bool)@unlink($tmpFilename))
                {
                    trigger_error(
                        sprintf(
                            '%s:removeHeadersAndMoveFile - Unable to remove temp file "%s"',
                            get_called_class(),
                            $tmpFilename
                        ),
                        E_USER_WARNING
                    );
                }

                return $outputFile;
            }

            throw new \RuntimeException(sprintf(
                '%s::removeHeadersAndMoveFile - Unable to open / create / truncate file at "%s".',
                get_called_class(),
                $outputFile
            ));
        }

        throw new \RuntimeException(sprintf(
            '%s::removeHeadersAndMoveFile - Unable to open temp file "%s".',
            get_called_class(),
            $tmpFilename
        ));
    }
}
