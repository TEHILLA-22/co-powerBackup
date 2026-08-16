package main

import (
	"fmt"
)

func main() {
    var task[] string;
    var taskInput string
    fmt.Println("Welcome to Tehillz task hub")

    fmt.Println("Enter a task to perform")
    fmt.Scan(&taskInput)
    task = append(task, taskInput)

    fmt.Printf("%v", task)

    
}
