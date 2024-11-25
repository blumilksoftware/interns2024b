import dayjs from 'dayjs'
import { formatDate } from '@/Helpers/Format'

export const converter = {
  question: {
    adminToUser(question: Question): UserQuestion {
      return {
        id: 1,
        text: question.text ?? '',
        answers: question.answers as UserAnswer[],
      }
    },
  },
  quiz: {
    adminToUser(quiz: Quiz): UserQuiz {
      return {
        title: quiz.title,
        quiz: quiz.id,
        closedAt: formatDate(
          dayjs(quiz.scheduledAt).add(quiz.duration ?? 0, 'minute'),
          false,
        ),
        questions: quiz.questions.map(
          converter.question.adminToUser,
        ),
      }
    },
  },
}
