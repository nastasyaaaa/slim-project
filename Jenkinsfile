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
                stage("FrontEnd") {
                    steps {
                        sh "sleep 1"
                    }
                }
            }
        }
        stage("Analyze") {
            steps {
                sh "sleep 1"
            }
        }
        stage("Test") {
            parallel {
                stage("API") {
                    steps {
                        sh "make api-test"
                    }
                }
                stage("FrontEnd") {
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