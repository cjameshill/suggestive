<template>
    <div class="row">
        <div class="col-md-8 col-md-push-2">
            <suggest-topic-button verb="Add"></suggest-topic-button>
            <h2>Suggested Topics</h2>

            <p v-show="suggestedTopics.length == 0">No suggested topics.</p>
            <div v-for="topic in suggestedTopics" class="row">
                <div class="col-xs-3 col-sm-2" style="text-align: right">
                    <div class="vote-count">
                        Votes:<br>
                        {{ topic.votes }}
                    </div>
                </div>
                <div class="col-xs-9 col-sm-10">
                    <div class="panel panel-default topic topic--in-list">
                        <div class="panel-heading"><h3 class="topic__title">{{ topic.title }}</h3></div>
                        <div class="panel-body">
                            {{ topic.description }}
                        </div>
                    </div>
                    <div class="btn-group">
                        <a @click.prevent="acceptTopic(topic)" class="btn btn-sm btn-primary">Accept</a>
                        <a @click.prevent="rejectTopic(topic)" class="btn btn-sm btn-danger">Reject</a>
                        <a @click.prevent="markTopicAsDuplicate(topic)" class="btn btn-sm btn-info">Mark as duplicate</a>
                    </div>
                    <br><br>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import Topics from './../topics.js';

    export default {
        data() {
            return {
                topics: [],
            }
        },
        computed: {
            suggestedTopics() {
                return this.topics.filter(topic => {
                    return topic.status == "suggested";
                });
            }
        },
        created() {
            Topics.all().then(topics => {
                this.topics = topics;
            });
        },
        methods: {
            flagTopic(topic, status) {
                Topics.flag(topic, status);
            },
            acceptTopic(topic) {
                this.flagTopic(topic, 'accepted');
            },
            rejectTopic(topic) {
                this.flagTopic(topic, 'rejected');
            },
            markTopicAsDuplicate(topic) {
                this.flagTopic(topic, 'duplicate');
            },
        }
    };
</script>
