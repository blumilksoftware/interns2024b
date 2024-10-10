import { nanoid } from 'nanoid'

const keyedObjects = new WeakSet()

export function keysWrapper<T extends object & {key?:string}>(objects: T[]): Array<T & {key:string}>{
  if (!keyedObjects.has(objects)) 
    for (const obj of objects)
      obj.key ??= nanoid()
  return objects as Array<T & {key:string}>
}

const itemsAsKeys = new WeakMap<any,string>()

export default function getKey(item: any) : string{
  if (!itemsAsKeys.has(item)) 
    itemsAsKeys.set(item, nanoid())
  return itemsAsKeys.get(item) ?? nanoid()
}
