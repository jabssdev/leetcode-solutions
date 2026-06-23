# Power of Two — Technical Notes

## Análisis de Complejidad

- **Tiempo: `O(1)`** — La solución consiste en exactamente dos operaciones bit a bit (`&` y `-`) y una comparación, todas de costo constante e independientes del valor de `n`. No existe ningún bucle, recursividad ni estructura de control iterativa. Es la complejidad temporal más favorable posible.

- **Espacio: `O(1)`** — No se crean estructuras de datos auxiliares, no hay call stack de recursión ni variables intermedias más allá de los registros del procesador. La solución opera íntegramente sobre los operandos en línea.

---

## Intuición y Enfoque

La técnica utilizada es **Bit Manipulation** — específicamente el truco `n & (n - 1)` para detectar si un número es potencia de dos.

**Premisa clave — representación binaria de potencias de 2:**

Toda potencia de dos tiene exactamente **un único bit activo (1)** en su representación binaria:

```
1  → 0001
2  → 0010
4  → 0100
8  → 1000
```

Cuando se le resta `1` a cualquier potencia de 2, el bit activo se apaga y todos los bits de menor peso se encienden:

```
4 - 1 = 3  →  0100 & 0011 = 0000
8 - 1 = 7  →  1000 & 0111 = 0000
```

Por el contrario, cualquier número que **no** sea potencia de 2 tendrá más de un bit activo, y `n & (n - 1)` producirá un resultado distinto de cero:

```
6 - 1 = 5  →  0110 & 0101 = 0100  ≠ 0
```

**La expresión completa:**

```js
n > 0 && (n & (n - 1)) === 0;
```

- `n > 0` descarta el caso borde de `n = 0`, que cumpliría `n & (n - 1) === 0` matemáticamente pero **no** es potencia de 2. También descarta negativos, que en complemento a dos nunca son potencias de 2.
- `(n & (n - 1)) === 0` valida que solo exista un bit activo.

**¿Por qué es óptima frente a la fuerza bruta?** La alternativa iterativa dividiría `n` entre 2 en un bucle `while (n % 2 === 0) n /= 2` en `O(log n)` pasos. Esta solución colapsa ese proceso a una única operación de hardware, aprovechando la estructura binaria del número directamente.

---

## Notas Políglotas (JavaScript vs PHP)

- **JavaScript:** El mayor riesgo aquí es la **precedencia de operadores**. El operador `&` tiene menor precedencia que `===` en JS, por lo que sin paréntesis `n & n - 1 === 0` se evaluaría como `n & (n - 1 === 0)`, produciendo un resultado incorrecto. Los paréntesis en `(n & (n - 1)) === 0` son **semánticamente obligatorios**, no opcionales. Adicionalmente, el operador `&` en JS convierte sus operandos a enteros de **32 bits con signo** antes de operar. Para el rango del problema (`-2³¹ ≤ n ≤ 2³¹`), esto es seguro, pero implica que valores mayores a `2³¹ - 1` serían truncados si se extrapolara la solución fuera del enunciado.

- **PHP:** La solución es idéntica carácter por carácter a la versión JS, lo que la convierte en un caso de portabilidad casi perfecta. Sin embargo, PHP presenta un comportamiento diferente en tamaño de enteros: en sistemas de **64 bits** (entornos PHP modernos estándar), los enteros nativos son de 64 bits, a diferencia del `int32` que impone JS con `&`. Para el rango del problema esto no produce diferencia, pero en un contexto de producción con valores grandes, PHP sería inherentemente más seguro para esta operación sin necesidad de manejo especial. La precedencia del operador `&` en PHP también es menor que la de `===`, por lo que los paréntesis son igualmente obligatorios.

> [!IMPORTANT]
> La expresión `(n & (n - 1)) === 0` sin los paréntesis exteriores es un bug silencioso en ambos lenguajes. La precedencia de `&` sobre `===` es una de las trampas más comunes en expresiones bit a bit mixtas con comparaciones. Siempre parentizar las operaciones bitwise cuando se combinan con operadores de igualdad.

---

## Lecciones Clave

- **Bit Manipulation como lookup de propiedades binarias en `O(1)`:** El truco `n & (n - 1)` es el representante más conocido de una familia de hacks bit a bit que explotan la aritmética binaria para detectar propiedades estructurales de un número (potencia de 2, número impar, bit más significativo, etc.) en tiempo constante. Memorizar y reconocer este patrón es fundamental para problemas de bajo nivel, sistemas embebidos, diseño de tablas hash, y cualquier contexto donde la eficiencia absoluta es crítica. Su contraparte más común es `n & (-n)`, que aísla el bit menos significativo activo (_Lowest Set Bit_), usado en estructuras como el **Fenwick Tree (Binary Indexed Tree)**.

- **Precedencia de operadores bitwise — la trampa del código correcto-pero-incorrecto:** Las operaciones bit a bit (`&`, `|`, `^`, `~`, `<<`, `>>`) tienen una precedencia sorprendentemente baja en C, JS, PHP y la mayoría de lenguajes derivados. Una expresión bitwise mezclada con comparaciones sin paréntesis es un bug que compila y ejecuta sin errores pero produce resultados incorrectos. Adoptar como regla de estilo: **siempre parentizar las subexpresiones bitwise**, independientemente de si los paréntesis son técnicamente necesarios.
