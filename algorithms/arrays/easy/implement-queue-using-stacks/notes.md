# Implement Queue Using Stacks — Technical Notes

## Análisis de Complejidad

| Operación | Tiempo Peor Caso | Tiempo Amortizado | Justificación                                                                                                                                            |
| --------- | ---------------- | ----------------- | -------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `push`    | `O(1)`           | `O(1)`            | Simple push al `inputStack`, sin ningún movimiento.                                                                                                      |
| `pop`     | `O(n)`           | `O(1)` amortizado | Solo mueve elementos de `inputStack` a `outputStack` cuando este último está vacío. Cada elemento es movido exactamente **una vez** en su ciclo de vida. |
| `peek`    | `O(n)`           | `O(1)` amortizado | Misma lógica que `pop` para el traslado; la lectura del tope es `O(1)`.                                                                                  |
| `empty`   | `O(1)`           | `O(1)`            | Comparación directa de tamaños de ambas estructuras.                                                                                                     |

- **Tiempo: `O(1)` amortizado** por operación — El análisis de complejidad amortizada (método del potencial) demuestra que aunque una operación individual `pop` o `peek` puede costar `O(n)` en el peor caso (cuando el `outputStack` está vacío), ese costo es pagado una sola vez por cada elemento. Cada elemento es insertado en `inputStack` una vez (`O(1)`), transferido a `outputStack` una vez (`O(1)` por elemento), y extraído de `outputStack` una vez (`O(1)`). El costo total para `n` operaciones es `O(n)`, es decir, `O(1)` por operación en promedio.

- **Espacio: `O(n)`** — Los dos stacks (`inputStack` y `outputStack`) almacenan en conjunto exactamente los `n` elementos actualmente en la queue. En ningún momento se duplican elementos; la transferencia vacía un stack para llenar el otro. No existe espacio adicional más allá de las dos estructuras y las variables locales del helper.

---

## Intuición y Enfoque

La técnica utilizada es **Lazy Transfer con Dos Stacks** — una estrategia de simulación de ADT que invierte el orden LIFO de un stack para emular el comportamiento FIFO de una queue, pagando el costo de inversión de forma diferida (_lazy_) y amortizada.

**Premisa clave — invertir el orden con dos stacks:**

Un stack es LIFO: el último en entrar es el primero en salir. Para emular una queue (FIFO), se necesita que el primer elemento insertado sea el primero en salir. La inversión de orden se logra vaciando el `inputStack` hacia el `outputStack`: al hacer pop de todos los elementos de un stack y pushearlos a otro, el orden queda completamente invertido.

```
Operaciones:  push(1), push(2), push(3), pop()

inputStack:   [1, 2, 3]   (3 en el tope)
outputStack:  []

→ _moveElements() transfiere solo si outputStack está vacío:

inputStack:   []
outputStack:  [3, 2, 1]   (1 en el tope → correcto para FIFO)

→ pop() retorna 1 ✓
```

**La guardia lazy en `_moveElements`:** La transferencia solo ocurre cuando `outputStack` está **completamente vacío**. Esta condición es la clave de la amortización: si `outputStack` aún tiene elementos, significa que el orden FIFO ya está preservado en él, y no se debe interrumpir ni mezclar con elementos nuevos del `inputStack`. Romper esta guardia introduciría elementos fuera de orden.

**¿Por qué es óptima frente a la alternativa naive?** Una implementación naive con un solo stack necesitaría invertir todos los elementos en cada `push` para mantener el orden (costo `O(n)` por inserción). La variante con dos stacks y transferencia lazy distribuye ese costo, logrando `O(1)` amortizado en todas las operaciones — un resultado que se puede demostrar óptimo para esta restricción de usar solo stacks.

---

## Notas Políglotas (JavaScript vs PHP)

- **JavaScript:** Los arrays nativos actúan simultáneamente como `inputStack` y `outputStack` mediante la combinación `.push()` (apilar) y `.pop()` (desapilar desde el tope). El helper se nombra con el prefijo convencional de underscore `_moveElements`, un acuerdo de la comunidad JS para señalar métodos de uso interno en objetos con prototipos, ya que `prototype`-based OOP no dispone de modificadores de acceso nativos (`private`). Para `peek`, el acceso al tope del `outputStack` se hace mediante índice directo: `this.outputStack[this.outputStack.length - 1]`, que es `O(1)` sobre arrays.

- **PHP:** Se utilizan dos instancias de `SplStack` de la Standard PHP Library, que implementan semántica LIFO estricta con interfaz de métodos explícitos (`push`, `pop`, `top`, `isEmpty`). A diferencia de JS donde `_moveElements` es una convención, en PHP el helper se declara `private`, que es un modificador de acceso real del lenguaje, garantizando encapsulamiento a nivel del runtime. Para `peek`, se utiliza `$this->outputStack->top()`, que devuelve el elemento en la cima del stack sin extraerlo — equivalente directo al acceso por índice de JS pero con una API semántica explícita. Los métodos de la clase tienen **type hints** completos (`int`, `void`, `bool`), elevando el contrato de la interfaz a tipado estático.

> [!NOTE]
> La asimetría de nomenclatura entre los dos lenguajes es reveladora: en JS el helper es `_moveElements` (pseudo-privado por convención), mientras en PHP es `moveElements` con modificador `private` real. El underscore en JS es un contrato social, no una garantía del lenguaje. En PHP, `private` es una garantía del compilador.

> [!IMPORTANT]
> `SplStack::top()` en PHP (usado en `peek`) **no extrae** el elemento — solo lo observa. Es el equivalente de `stack[-1]` en Python o `arr[arr.length - 1]` en JS. No confundir con `pop()`, que sí extrae y destruye el elemento del tope.

---

## Lecciones Clave

- **Amortización como herramienta de diseño, no solo de análisis:** La transferencia lazy de `inputStack` a `outputStack` no es un detalle de implementación, es la **decisión de diseño central** que convierte un `O(n)` por operación en `O(1)` amortizado. Aplicar este patrón cuando se diseñe cualquier estructura de datos con operaciones cuyo costo puede diferirse: buffers de escritura, batching de operaciones costosas, o lazy evaluation. El principio es siempre el mismo: pagar el costo una sola vez, en el momento correcto, en lugar de pagarlo en cada operación.

- **Simetría del par Stack↔Queue:** Este problema es el simétrico exacto de _Implement Stack Using Queues_ (que usa una sola queue con rotación en `push`). La diferencia arquitectónica es que este diseño de dos stacks es **más eficiente**: logra `O(1)` amortizado en todas las operaciones, mientras que la variante de stack-con-queue tiene un `push` de `O(n)` no amortizable. Cuando se implemente una de estas simulaciones en producción, este diseño de dos stacks es siempre la elección correcta.
