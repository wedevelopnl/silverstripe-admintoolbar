/** Replaces `{name}` tokens with their value; a token without a value stays literal. */
export function format(template: string, values: Record<string, string | number>): string {
  return template.replace(/\{(\w+)\}/g, (token, name: string) =>
    Object.hasOwn(values, name) ? String(values[name]) : token,
  )
}
