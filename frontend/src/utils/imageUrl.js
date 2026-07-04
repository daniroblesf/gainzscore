// Maps known German exercise names (lowercase) to their local filename.
// Used as a fallback when exercise.image is null/empty.
const NAME_TO_FILE = {
  'bankdrücken':               'bench-press.png',
  'schrägbankdrücken':         'incline-barbell-bench-press.png',
  'liegestütze':               'push-up.png',
  'latziehen':                 'lat-pulldown-machine.png',
  'kurzhantelrudern':          'db-row.png',
  'klimmzüge':                 'pull-up.png',
  'bizeps curls':              'bicep-curl.png',
  'trizeps drücken overhead':  'triceps-overhead.png',
  'hammer curls':              'hammer-curl.png',
  'kniebeugen':                'squats.png',
  'beinpresse':                'leg-press.png',
  'ausfallschritte':           'lunges.png',
}

const CUSTOM_EXERCISE_IMAGE = '/GainzScore Mini-Logo.png'

/**
 * Returns the local public path for an exercise image.
 * The DB already stores the correct path (e.g. "/exercises/bench-press.png"),
 * which Vite serves directly from frontend/public/ — no prefix needed.
 * Falls back to a name-based lookup if image is null.
 */
export function getExerciseImage(exercise) {
  if (!exercise) return null

  if (exercise.image) return exercise.image

  const key      = exercise.name?.toLowerCase().trim()
  const filename = NAME_TO_FILE[key]
  if (filename) return `/exercises/${filename}`

  if (exercise.category?.toLowerCase().trim() === 'custom') {
    return CUSTOM_EXERCISE_IMAGE
  }

  if (exercise.user_id) {
    return CUSTOM_EXERCISE_IMAGE
  }

  return null
}
