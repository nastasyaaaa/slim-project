pipeline {
    agent any
    options {
        timestamps()
    }
    environment {
        CI = 'true'
    }
    stages {
        stage("Init") {
            steps {
                sh "make init"
            }
        }
        stage("Validate schema") {
            steps {
                sh "make validate-schema"
            }
        }
        stage("Lint") {
            parallel {
                stage("API") {
                    steps {
                        sh "sleep 1"
                    }
                }
                stage("Front End") {
                    steps {
                        sh "sleep 1"
                    }
                }
            }
        }
        stage("Down") {
            steps {
                sh "make down"
            }
        }
    }
    post {
        always {
            sh "make down || true"
        }
    }
}