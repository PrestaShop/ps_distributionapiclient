/**
 * GitHub's `company` field is free text, and the convention there is to prefix an organisation with
 * `@` so the profile page can turn it into a link. Rendered as a plain label the sigil is noise, and
 * the field also arrives with stray whitespace.
 *
 * Only an `@` that opens a word is dropped, so a value naming several organisations reads as a list
 * instead of keeping the marker on all but the first, while an `@` used as a word ("Web developer @
 * Semantic") or inside one (an email address) is left alone. Nothing else about the text is touched:
 * it is free text, and rewriting more of it would be guesswork.
 */
export const formatCompanyName = (company: string): string =>
  company.replace(/(^|\s)@(?=\S)/g, '$1').trim()
