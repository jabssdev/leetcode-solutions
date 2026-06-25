# Assign Cookies — Technical Notes

> **LeetCode #455** · Difficulty: Easy · Topic: Array, Greedy, Sorting

---

## Análisis de Complejidad

Donde `n = g.length` (niños) y `m = s.length` (galletas):

| Dimensión   | Notación               | Justificación                                                                                                                                                                                                                                                                                                                                         |
| ----------- | ---------------------- | ----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| **Tiempo**  | `O(n log n + m log m)` | El costo dominante es el ordenamiento de ambos arrays: `O(n log n)` para `g` y `O(m log m)` para `s`. El bucle `while` posterior recorre en a lo sumo `n + m` iteraciones totales (cada puntero avanza monotónicamente hacia adelante y nunca retrocede), contribuyendo `O(n + m)`. El costo total está dominado por el sort: `O(n log n + m log m)`. |
| **Espacio** | `O(log n + log m)`     | No se crean estructuras auxiliares en heap. El único costo de memoria es el **Call Stack** consumido por el algoritmo de sort interno (Timsort / Introsort), que usa `O(log n)` y `O(log m)` de espacio de pila respectivamente para sus fases de mergesort recursivo. Las variables escalares `childI`, `cookieJ` son `O(1)`.                        |

---

## Intuición y Enfoque

### Técnica: Greedy + Two Pointers sobre Arrays Ordenados

**¿Por qué Greedy es correcto aquí?**

La estrategia greedy óptima es: _asignar a cada niño (procesado de menor a mayor avidez) la galleta más pequeña que aún pueda satisfacerlo_. La justificación formal por **exchange argument** es:

Supongamos que existe una asignación óptima diferente que no sigue este orden. Entonces existe un niño `A` (menos ávido) que recibió una galleta grande, y un niño `B` (más ávido) que recibió una galleta pequeña o ninguna. Si intercambiamos las galletas entre `A` y `B`, `A` sigue satisfecho (recibe una galleta más pequeña pero suficiente) y `B` puede ahora satisfacerse (o seguir sin galleta, sin empeorar). El número de niños satisfechos no disminuye. Por inducción, la estrategia greedy produce un resultado al menos tan bueno como cualquier otro: es **óptima**.

**Mecanismo de operación:**

Ambos arrays se ordenan de forma ascendente como prerrequisito. Luego dos punteros recorren `g` (niños) y `s` (galletas) simultáneamente:

- `cookieJ` avanza **siempre** en cada iteración: representa probar cada galleta exactamente una vez, de la más pequeña a la más grande.
- `childI` avanza **solo si** la galleta actual `s[cookieJ]` satisface al niño actual `g[childI]` (es decir, `s[cookieJ] >= g[childI]`). Cada avance de `childI` representa un niño satisfecho.

Si una galleta no es suficiente para el niño actual, se descarta (el puntero de galletas avanza de todas formas) y se prueba la siguiente. Si es suficiente, se asigna y ambos punteros avanzan conceptualmente (el de galletas siempre, el de niños condicionalmente).

**Retorno:** `childI` al finalizar contiene el número total de niños satisfechos, ya que solo se incrementó cuando se confirmó una asignación exitosa.

**¿Por qué es correcto no retroceder los punteros?** El ordenamiento garantiza que si la galleta actual no satisface al niño actual (el menos exigente de los restantes), tampoco satisfará a ningún niño más exigente. La galleta puede descartarse de forma segura.

---

## Notas Políglotas (JavaScript vs PHP)

### JavaScript

- `g.sort((a, b) => a - b)` utiliza un **comparador aritmético explícito** para ordenar de forma numérica ascendente. Este detalle es crítico en JS: sin el comparador, `Array.prototype.sort()` convierte los elementos a strings y los ordena lexicográficamente, lo que produce resultados incorrectos para arrays de enteros (ej: `[10, 9, 2]` se ordenaría como `[10, 2, 9]` lexicográficamente). El comparador `(a, b) => a - b` retorna negativo si `a < b`, cero si iguales, y positivo si `a > b`, que es el contrato que `sort` requiere.
- `Array.prototype.sort()` en JS es **in-place y mutable**: modifica el array original directamente. El motor V8 de Node.js implementa **Timsort** (híbrido de merge sort e insertion sort), que garantiza `O(n log n)` en el peor caso y `O(n)` para arrays casi ordenados.
- El bucle `while` con la doble condición `childI < g.length && cookieJ < s.length` accede a `.length` en cada evaluación. En V8, `.length` es una propiedad optimizada de acceso `O(1)` almacenada como metadato del array, sin costo de recálculo.

### PHP

- `sort($g)` y `sort($s)` son funciones nativas de PHP que ordenan arrays de forma ascendente **in-place**. A diferencia de JS, `sort()` en PHP realiza comparaciones numéricas correctas para arrays de enteros sin necesidad de un comparador personalizado: PHP infiere el tipo de comparación basándose en los valores (`SORT_REGULAR` por defecto, que usa comparación numérica para enteros).
- `sort()` en PHP implementa internamente **Introsort** (híbrido de quicksort, heapsort e insertion sort), garantizando `O(n log n)` en el peor caso.
- Los tamaños `count($g)` y `count($s)` se pre-calculan en `$numChildren` y `$numCookies` antes del bucle `while`. Esta es una optimización explícita: evita que `count()` sea evaluada en cada iteración de la condición del `while`. A diferencia de JS donde `.length` es una propiedad de acceso directo, `count()` en PHP es una llamada a función, aunque en versiones modernas (PHP 7+) el compilador JIT puede evitar la re-evaluación.
- PHP pasa los arrays `$g` y `$s` **por valor** (copy-on-write). `sort()` recibe una referencia implícita solo cuando opera sobre una variable pasada directamente (PHP hace la referencia internamente para la modificación in-place). El resultado es funcionalmente idéntico al JS: los arrays originales del llamador se modifican.

---

## Lecciones Clave

- **Patrón "Greedy sobre secuencias ordenadas":** El prerequisito de ordenamiento transforma un problema de asignación combinatoria (`O(n·m)` pares posibles) en un recorrido lineal con decisiones locales óptimas. Este patrón es la firma del diseño Greedy: _si puedo demostrar que la decisión localmente óptima nunca cierra el camino a la solución globalmente óptima (exchange argument), el algoritmo Greedy es correcto_. Aplicarlo siempre que el problema involucre asignación de recursos con restricciones de capacidad, scheduling de tareas, o emparejamiento de colecciones heterogéneas donde el orden de procesamiento puede fijarse sin pérdida de optimalidad.

- **Two Pointers como motor de recorrido Greedy:** Una vez que ambas secuencias están ordenadas, los dos punteros son el mecanismo de ejecución natural de la estrategia greedy: el puntero de "recursos" avanza siempre (consume o descarta), y el puntero de "demanda" avanza solo ante una asignación exitosa. Esta asimetría de avance — _uno siempre, otro condicionalmente_ — aparece en problemas de merge de arrays ordenados, validación de subsecuencias, y algoritmos de sweepline en geometría computacional. Reconocer este patrón de movimiento asimétrico reduce inmediatamente la complejidad de diseño del algoritmo.
