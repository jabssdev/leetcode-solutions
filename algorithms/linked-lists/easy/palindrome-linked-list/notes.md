# Palindrome Linked List — Technical Notes

## Análisis de Complejidad

- **Tiempo: `O(n)`** — La solución realiza exactamente **tres pasadas lineales** sobre la lista, cada una de costo `O(n)`:
  1. **Fase 1** (Fast & Slow): `n/2` iteraciones para localizar el punto medio.
  2. **Fase 2** (Reversión): `n/2` iteraciones para revertir la segunda mitad in-place.
  3. **Fase 3** (Comparación): `n/2` iteraciones para comparar ambas mitades.

  Las tres fases son secuenciales y no anidadas, por lo que el costo total es `3 × O(n/2) = O(n)`.

- **Espacio: `O(1)`** — La solución opera **in-place** sobre la lista original. Las únicas variables auxiliares son punteros escalares (`slow`, `fast`, `prev`, `curr`, `left`, `right`, `nextTemp`), cuyo número es constante e independiente de `n`. No se crean arrays, stacks, ni ninguna estructura proporcional a la entrada. Esta es la solución espacialmente óptima para este problema.

---

## Intuición y Enfoque

La técnica combina tres patrones clásicos de listas enlazadas en secuencia: **Floyd's Tortoise & Hare** para encontrar el punto medio, **In-Place Reversal** para la segunda mitad, y **Two Pointers** convergentes para la comparación.

**¿Por qué no simplemente copiar a un array?** La alternativa `O(n)` espacio sería volcar todos los valores a un array y verificar si es palíndromo con dos punteros desde los extremos. Sin embargo, esta solución logra el mismo `O(n)` tiempo con `O(1)` espacio, que es el resultado óptimo demostrable para este problema.

---

**Fase 1 — Floyd's Tortoise & Hare (localizar el punto medio):**

`slow` avanza un nodo por iteración; `fast` avanza dos. Cuando `fast` llega al final, `slow` está exactamente en el nodo medio. Para listas de longitud par, `slow` queda en el primer nodo de la segunda mitad.

```
Lista: [1 → 2 → 2 → 1]

Iter 1: slow=2, fast=2(segundo)
Iter 2: slow=2(tercero), fast=null → bucle termina

slow apunta al inicio de la segunda mitad ✓
```

**Fase 2 — Reversión in-place de la segunda mitad:**

A partir de `slow`, se revierte la dirección de los punteros `next` nodo a nodo. Se mantiene `prev` como el nodo "ya revertido" y `curr` como el nodo en proceso. Al finalizar, `prev` apunta al **último nodo original**, que ahora es la nueva cabeza de la segunda mitad revertida.

```
Antes:   [2 → 1]  (segunda mitad)
Después: [1 → 2]  (revertida, prev = nodo "1")
```

**Fase 3 — Comparación con Two Pointers:**

`left` parte de `head` (inicio de la primera mitad); `right` parte de `prev` (inicio de la segunda mitad revertida). Ambos avanzan en paralelo comparando valores. Si en algún punto `left.val !== right.val`, no es palíndromo. El bucle termina cuando `right` llega a `null` (la segunda mitad es igual o menor en longitud que la primera en listas de longitud impar).

---

## Notas Políglotas (JavaScript vs PHP)

- **JavaScript:** El guard clause inicial `if (!head || !head.next) return true` usa evaluación de truthiness: `!head` es `true` cuando `head` es `null` o `undefined`, y `!head.next` verifica que la lista no sea de un solo nodo. Es idiomático y conciso, pero aprovecha la coerción implícita de JS (falsy). La variable `nextTemp` se declara con `const` dentro del bucle, ya que se reasigna en cada iteración creando una nueva vinculación léxica por scope de bloque — un patrón correcto y deliberado en ES6+.

- **PHP:** El guard clause utiliza comparaciones de igualdad estricta `=== null` en lugar de truthy/falsy, lo cual es la práctica correcta en PHP para objetos y referencias nulas: `$head === null || $head->next === null`. El acceso a propiedades de objeto se realiza con el operador `->` en lugar de `.` de JS. La variable `$nextTemp` no tiene un scope de bloque en PHP (las variables de PHP tienen scope de función), pero su comportamiento es funcionalmente equivalente al `const` de JS ya que se sobreescribe en cada iteración antes de usarse. La comparación de valores `$left->val !== $right->val` utiliza el operador de no-igualdad estricta, alineado con el tipado explícito del lenguaje.

> [!IMPORTANT]
> Esta solución **muta la estructura original de la lista** al revertir la segunda mitad. El enunciado de LeetCode lo permite, pero en un contexto de producción esto podría ser un efecto secundario inaceptable si otros componentes mantienen referencias a nodos de la lista. Una solución robusta restauraría la lista a su estado original revirtiendo la segunda mitad nuevamente antes de retornar. Ninguna de las dos implementaciones realiza esta restauración.

---

## Lecciones Clave

- **Composición de patrones como solución óptima:** Este problema ilustra que las soluciones más eficientes en listas enlazadas frecuentemente combinan múltiples patrones clásicos en secuencia (Fast & Slow → Reversión → Two Pointers) en lugar de aplicar uno solo. Reconocer cuándo un problema requiere esta composición es una habilidad de nivel senior. Aplicar este enfoque en problemas como _Reorder List_, _Linked List Cycle II_, o cualquier tarea que requiera operar sobre dos mitades de una lista simultáneamente.

- **In-Place Reversal como alternativa a `O(n)` espacio auxiliar:** Siempre que la fuerza bruta de un problema de lista enlazada requiera copiar valores a un array u otra estructura, evaluar si la reversión in-place puede eliminar ese espacio auxiliar. El patrón de tres punteros (`prev = null`, `curr = head`, `nextTemp = curr.next`) es la plantilla canónica para revertir una lista o sublista en `O(1)` espacio, y debe memorizarse como primitiva fundamental de manipulación de listas enlazadas.
