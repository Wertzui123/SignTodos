# SignTodos
A new way to make todo-lists in minecraft!
<br>You can download the .phar from <a href="https://poggit.pmmp.io/p/SignTodos">poggit</a>.

SignTodos is a new approach to todo-lists for minecraft buildings.
<br>It lets you create them inside the game just using a sign.
<br>You can later view all your todos and teleport to the signs using a command.

# How to use?
1. Place a sign
2. Write something like "//TODO: Finish this" on it (see [Todo pattern](#Todo pattern))
3. You can now view all your todo-signs with /todos

# Todo pattern
The pattern for todos is this:

```/^\/\/ *todo: */i```

You probably have no idea what that means, so let's take a look at this example:

<b>// todo: example todo</b> (any amount of space between `//`, `todo` and after the `:` is valid)
<br>Also, whether the word "todo" is uppercase or lowercase doesn't matter.

## Example todo-signs
//todo: download the plugin
<br>// todo: add it to the server
<br>// TODO: Restart the server

# Bugs
Please open an <a href="https://github.com/Wertzui123/SignTodos/issues">issue</a> or join my <a href="https://discord.gg/dmWPEYq">discord server</a>.

# Commands
| Command | Description          | Usage  | Aliases | Permission              |
|---------|----------------------|--------|---------|-------------------------|
| todos   | Shows all your todos | /todos | /       | signtodos.command.todos |

# Credits
SignTodos was made by Wertzui123.