type Sortable = ({ name: string } | { title: string }) & { createdAt: string, updatedAt: string }
interface SortOptionConstructor { text: string, type: 'name' | 'title' | 'creationDate' | 'modificationDate', desc?: boolean }
interface SortOption { key: string, text: string, action: () => void }
type Sorter<T extends Sortable> = (a: T, b: T) => number;
