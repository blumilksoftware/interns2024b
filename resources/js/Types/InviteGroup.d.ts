interface InviteGroup {
  users: Array<Ranking & { school_id: number }>
  school: School
  points: number
}
