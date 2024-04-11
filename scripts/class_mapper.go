package main

import (
    "flag"
    "fmt"
    "io/ioutil"
    "os"
    "path/filepath"
    "regexp"
    "strings"
)

func main() {
    var startDir, outputPath string
    flag.StringVar(&startDir, "dir", ".", "Path to the directory to scan")
    flag.StringVar(&outputPath, "out", "classes.php", "Path for the output PHP file")
    flag.Parse()

    var classes []string // A slice to hold all the class names

    namespaceRegex := regexp.MustCompile(`namespace\s+([a-zA-Z0-9\\_]+);`)
    classRegex := regexp.MustCompile(`(class|interface|trait|enum)\s+([a-zA-Z0-9_]+)`)

    filepath.Walk(startDir, func(path string, info os.FileInfo, err error) error {
        if err != nil {
            fmt.Println(err)
            return nil
        }
        if !info.IsDir() && filepath.Ext(path) == ".php" {
            content, err := ioutil.ReadFile(path)
            if err != nil {
                fmt.Println(err)
                return nil
            }

            nsMatches := namespaceRegex.FindStringSubmatch(string(content))
            classMatches := classRegex.FindAllStringSubmatch(string(content), -1)

            if len(nsMatches) > 1 {
                ns := nsMatches[1]
                for _, match := range classMatches {
                    if len(match) > 2 {
                        className := match[2]
                        fullClassName := ns + "\\" + className
                        classes = append(classes, fullClassName) // Add the full class name to the slice
                    }
                }
            }
        }
        return nil
    })

    // Start building the PHP file content with strict types declaration and PHPDoc
    var sb strings.Builder
    sb.WriteString("<?php\n\n")
    sb.WriteString("declare(strict_types=1);\n\n")
    sb.WriteString("/** @return array<string> */\n")
    sb.WriteString("return [\n")
    for _, class := range classes {
        sb.WriteString(fmt.Sprintf("    '%s',\n", class))
    }
    sb.WriteString("];\n\n") // Ensure the final newline is included

    // Write the PHP content to the specified output file
    if err := ioutil.WriteFile(outputPath, []byte(sb.String()), 0644); err != nil {
        fmt.Println(err)
    } else {
        fmt.Printf("PHP classes list generated successfully: %s\n", outputPath)
    }
}
