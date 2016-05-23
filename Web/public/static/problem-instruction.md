# Markdown简明指南
XenOnline系统的题目内容使用Markdown格式。在这个编辑器上，你可以在左侧输入Markdown源码，右侧将实时同步生成内容。
Markdown语法简单，功能实用而强大。你可以通过阅读本指南来学习Markdown。

## Markdown的优点
Markdown具有很多优点，非常适合文字的排版。

> 简单的语法
> 强大的功能
> 源码具有极高的可读性
> 便于储存、交流

## Markdown的用法
Markdown十分容易学习。你可以轻松的让字体成为**粗体**或*斜体*。
Markdown当然不止如此。

### 你还可以

1. 列出一份清单。
2. 你可以自己顺序标号。
2. 即使你的编号错误，它也会自动纠正。

### 它还可以

- [ ] 做待办事项清单。
- [x] 这件事做完了~

### 表格也没问题

| 项目        | 价格   |  数量  |
| --------   | -----:  | :----:  |
| 计算机     | \$1600 |   5     |
| 手机        |   \$12   |   12   |
| 管线        |    \$1    |  234  |

### 当然，怎能少了方便地码代码

```C++
#include <cstdio>

int main()
{
	int a, b;
	scanf("%d%d", &a, &b);
	printf("%d", a + b);
}
```

### 流程图想画也是可以的：

```flow
st=>start: Start
op=>operation: Your Operation
cond=>condition: Yes or No?
e=>end

st->op->cond
cond(yes)->e
cond(no)->op
```

### 其实序列图我不会：

```seq
Alice->Bob: Hello Bob, how are you?
Note right of Bob: Bob thinks
Bob-->Alice: I am good thanks!
```

### 你还可以写公式：

$$E=mc^2$$

好了，以上就是Markdown的主要功能。

## 更多

更多信息，请访问Markdown在维基百科的[条目](https://zh.wikipedia.org/wiki/Markdown)。

![XenOnline](https://raw.githubusercontent.com/moycat/XenOnline/master/XenOnline.png)